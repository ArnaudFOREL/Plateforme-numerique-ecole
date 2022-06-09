<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Course;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\Works;
use App\Entity\User;
use App\Entity\Homepage;
use App\Entity\Programs;
use App\Form\PostType;
use App\Form\CommentsType;
use App\Form\AdminUserType;
use App\Form\HomepageType;
use App\Form\ProgramType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\CourseRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\ClassroomRepository;
use App\Repository\WorksRepository;
use App\Repository\HomepageRepository;
use App\Repository\ProgramsRepository;
use App\Security\PostVoter;
use App\Events\NewMessageWorkEvent;
use App\Events\WorkReadEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/admin")
 * @IsGranted("ROLE_TEACHER")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BlogController extends AbstractController
{
    private $manager;

    private $commentrepository;

    private $courserepository;

    private $categoryrepository;

    private $userrepository;

    private $classroomrepository;

    private $worksrepository;

    private $passwordEncoder;

    private $homepagerepository;

    private $programsrepository;

    public function __construct(EntityManagerInterface $manager, CommentRepository $commentrepository, CourseRepository $courserepository, CategoryRepository $categoryrepository, UserRepository $userrepository, ClassroomRepository $classroomrepository, WorksRepository $worksrepository, UserPasswordEncoderInterface $passwordEncoder, HomepageRepository $homepagerepository, ProgramsRepository $programsrepository)
    {
        $this->em = $manager;
        $this->commentrepository = $commentrepository;
        $this->courserepository = $courserepository;
        $this->categoryrepository = $categoryrepository;
        $this->userrepository = $userrepository;
        $this->classroomrepository = $classroomrepository;
        $this->worksrepository = $worksrepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->homepagerepository = $homepagerepository;
        $this->programsrepository = $programsrepository;
    }

    /**
     * Lists all Post entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_post_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *   * 'admin_index' is a nice shortcut to the backend homepage. This allows
     *     to create simpler links in the templates. Moreover, in the future we
     *     could move this annotation to any other controller while maintaining
     *     the route name and therefore, without breaking any existing link.
     *
     * @Route("/{page}", name="admin_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
     * @Route("/{page}", name="admin_post_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
     */
    public function index($page, PostRepository $posts, PaginatorInterface $paginator, Category $category = null, Request $request, SluggerInterface $slugger): Response
    {
        $authorPosts = $posts->findBy(['author' => $this->getUser()], ['publishedAt' => 'DESC']);

        $categories = $this->categoryrepository->findAll();

        $courses = array();

        foreach ($categories as $category) {
            $courses[$category->getId()] = $paginator->paginate(
                $this->courserepository->findByCategory($category),
                $page, /*page number*/
                10 /*limit per page*/
            );
        }

        $users = $this->userrepository->findAll();

        $classrooms = $this->classroomrepository->findAll();

        $works = $this->worksrepository->findAll();

        $newUser = new User();

        $form = $this->createForm(AdminUserType::class, $newUser);
        $form->handleRequest($request);

        $errorUsername = $form['username']->getErrors();

        if (count($errorUsername) > 0) {
            $this->addFlash('danger', 'Ce nom d\'utilisateur existe déjà ! ('. $form['username']->getData() .')');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if (in_array('ROLE_STUDENT', $form['roles']->getData())
                                        AND $form['classroom']->getData()->getId() == 99
                                        OR $form['classroom']->getData()->getId() == 100
                                        OR $form['classroom']->getData()->getId() == 110
                                        OR $form['classroom']->getData()->getId() == 120
                                        OR $form['classroom']->getData()->getId() == 130
                                        OR $form['classroom']->getData()->getId() == 140) {
                $this->addFlash('danger', 'Vous ne pouvez pas choisir ce groupe d\'élèves ! ('. $form['classroom']->getData()->getName() .')');
                return $this->redirectToRoute('admin_index');
            }

            $newUser->setEmail('');
            if (in_array('ROLE_STUDENT', $form['roles']->getData())) {
                $roles['Role'] = 'ROLE_STUDENT';
                $newUser->setRoles($roles);
            } elseif (in_array('ROLE_TEACHER', $form['roles']->getData())) {
                $roles['Role'] = 'ROLE_TEACHER';
                $newUser->setRoles($roles);
            } else {
                $roles['Role'] = 'ROLE_USER';
                $newUser->setRoles($roles);
            }

            if (in_array('ROLE_TEACHER', $newUser->getRoles())) {
                $newUser->setClassroom(null);
            }
            $newUser->setConnectedAt(new \Datetime());
            $encodedPassword = $this->passwordEncoder->encodePassword($newUser, $form['plainPassword']->getData());
            $newUser->setPassword($encodedPassword);

            $this->em->persist($newUser);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur '. $form['firstname']->getData() .' '. $form['name']->getData() .' créé !');

            return $this->redirectToRoute('admin_index');

        }

        $homepage = $this->homepagerepository->findLast();

        $formPage = $this->createForm(HomepageType::class, $homepage);
        $formPage->handleRequest($request);

        if ($formPage->isSubmitted() && $formPage->isValid()) {

            $ds = DIRECTORY_SEPARATOR;

            $files = $request->files->get('homepage');

            if ($files['pageImage'] && $formPage['pageImage']->getData() != null) {

                $file = $files['pageImage'];

                // set your uploads directory
                $uploadDir = $this->getParameter('images_directory_homepage');

                $extension = $file->guessExtension();

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                 mkdir($uploadDir);
                }
                if ($file->move($uploadDir, $fileName)) {
                    // create and set this image
                    $homepage->setPageImage($fileName);

                    // save the uploaded filename to database
                    $this->em->persist($homepage);
                }
            }

            if ($formPage['content']->getData() != null) {
                $homepage->setContent($formPage['content']->getData());
            }

            $this->em->persist($homepage);
            $this->em->flush();

            $this->addFlash('success', 'Page d\'accueil modifiée !');

            return $this->redirectToRoute('admin_index');

        }



        return $this->render('admin/blog/index.html.twig', [
            'posts' => $authorPosts,
            'courses' => $courses,
            'categories' => $categories,
            'users' => $users,
            'classrooms' => $classrooms,
            'works' => $works,
            'page' => $homepage,
            'form' => $form->createView(),
            'formPage' => $formPage->createView(),
        ]);
    }

    /**
     * @Route("/deleteImg{img}/{id}", name="home_deleteImg")
     * @isGranted("ROLE_TEACHER")
     *
     * Fonction pour supprimer les images de la page d'accueil
     */
    public function deleteImageHome(Homepage $homepage, $img)
    {
        $ds = DIRECTORY_SEPARATOR;
        // set your uploads directory
        if ($img == "principale") {
            $uploadDir = $this->getParameter('images_directory_homepage');
            $prevImage = $uploadDir.$ds.$homepage->getPageImage();
            $homepage->setPageImage(null);
        } elseif ($img == "cp") {
            $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs'.$ds.'Images';
            $prevImage = $uploadDir.$ds.$homepage->getImageCP();
            $homepage->setImageCP(null);
        } elseif ($img == "ce1") {
            $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs'.$ds.'Images';
            $prevImage = $uploadDir.$ds.$homepage->getImageCE1();
            $homepage->setImageCE1(null);
        }

		// Suppression de l'image sur le serveur
		if (file_exists($prevImage) && !is_dir($prevImage) ) {
			unlink($prevImage);
		}

		$this->em->flush();

		$this->addFlash('success', 'Image supprimée avec succès !');

        return $this->redirectToRoute('admin_index');
    }

    /**
     * Fonction pour modifier les programmes
     *
     * @Route("/programs", name="admin_programs")
     */
    public function addAndEditPrograms(Request $request, SluggerInterface $slugger)
    {
        $program = $this->programsrepository->findLastPrograms();


        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ds = DIRECTORY_SEPARATOR;

            $files = $request->files->get('program');

            if ($files) {

                if ($files['cp_program1'] && $form['cp_program1']->getData() != null) {

                    $file = $files['cp_program1'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCpProgram1($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $cpProgram1 = $program->getCpProgram1();
                    if($cpProgram1 != null) {
                        $program->setCpProgram1($cpProgram1);
                        $this->em->persist($program);
                    }
                }

                if ($files['cp_program2'] && $form['cp_program2']->getData() != null) {

                    $file = $files['cp_program2'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCpProgram2($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $cpProgram2 = $program->getCpProgram2();
                    if($cpProgram2 != null) {
                        $program->setCpProgram2($cpProgram2);
                        $this->em->persist($program);
                    }
                }

                if ($files['ce1_program1'] && $form['ce1_program1']->getData() != null) {

                    $file = $files['ce1_program1'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCe1Program1($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $ce1Program1 = $program->getCe1Program1();
                    if($ce1Program1 != null) {
                        $program->setCe1Program1($ce1Program1);
                        $this->em->persist($program);
                    }
                }

                if ($files['ce1_program2'] && $form['ce1_program2']->getData() != null) {

                    $file = $files['ce1_program2'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCe1Program2($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $ce1Program2 = $program->getCe1Program2();
                    if($ce1Program2 != null) {
                        $program->setCe1Program2($ce1Program2);
                        $this->em->persist($program);
                    }
                }

                if ($files['ce1_program3'] && $form['ce1_program3']->getData() != null) {

                    $file = $files['ce1_program3'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCe1Program3($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $ce1Program3 = $program->getCe1Program3();
                    if($ce1Program3 != null) {
                        $program->setCe1Program3($ce1Program3);
                        $this->em->persist($program);
                    }
                }

                if ($files['ce2_program1'] && $form['ce2_program1']->getData() != null) {

                    $file = $files['ce2_program1'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCe2Program1($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $ce2Program1 = $program->getCe2Program1();
                    if($ce2Program1 != null) {
                        $program->setCe2Program1($ce2Program1);
                        $this->em->persist($program);
                    }
                }

                if ($files['ce2_program2'] && $form['ce2_program2']->getData() != null) {

                    $file = $files['ce2_program2'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCe2Program2($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $ce2Program2 = $program->getCe2Program2();
                    if($ce2Program2 != null) {
                        $program->setCe2Program2($ce2Program2);
                        $this->em->persist($program);
                    }
                }

                if ($files['cm1_program1'] && $form['cm1_program1']->getData() != null) {

                    $file = $files['cm1_program1'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCm1Program1($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $cm1Program1 = $program->getCm1Program1();
                    if($cm1Program1 != null) {
                        $program->setCm1Program1($cm1Program1);
                        $this->em->persist($program);
                    }
                }

                if ($files['cm1_program2'] && $form['cm1_program2']->getData() != null) {

                    $file = $files['cm1_program2'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCm1Program2($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $cm1Program2 = $program->getCm1Program2();
                    if($cm1Program2 != null) {
                        $program->setCm1Program2($cm1Program2);
                        $this->em->persist($program);
                    }
                }

                if ($files['cm2_program1'] && $form['cm2_program1']->getData() != null) {

                    $file = $files['cm2_program1'];

                    // set your uploads directory
                    $uploadDir = $this->getParameter('images_directory_homepage').$ds.'Programs';

                    $extension = $file->guessExtension();

                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

                    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                        mkdir($uploadDir);
                    }
                    if ($file->move($uploadDir, $fileName)) {
                        // create and set this image
                        $program->setCm2Program1($fileName);

                        // save the uploaded filename to database
                        $this->em->persist($program);
                    }
                } else {
                    $cm2Program1 = $program->getCm2Program1();
                    if($cm2Program1 != null) {
                        $program->setCm2Program1($cm2Program1);
                        $this->em->persist($program);
                    }
                }

                $this->em->flush();

                $this->addFlash('success', 'Programme enregistré !');

                return $this->redirectToRoute('admin_index');

            } else {
                $this->addFlash('warning', 'ERREUR : Aucun fichier envoyé !');
            }

        }


        return $this->render('admin/blog/programs.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/newPost", name="admin_post_new")
     *
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $post->setAuthor($this->getUser());

        // See https://symfony.com/doc/current/form/multiple_buttons.html
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/forms.html#processing-forms
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($post);
            $this->em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Post entity.
     *
     * @Route("/viewPost{id<\d+>}", methods="GET", name="admin_post_show")
     */
    public function show(Post $post): Response
    {
        // This security check can also be performed
        // using an annotation: @IsGranted("show", subject="post", message="Posts can only be shown to their authors.")
        $this->denyAccessUnlessGranted(PostVoter::SHOW, $post, 'Posts can only be shown to their authors.');

        return $this->render('admin/blog/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/editPost{id<\d+>}", methods="GET|POST", name="admin_post_edit")
     * @IsGranted("edit", subject="post", message="Posts can only be edited by their authors.")
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'post.updated_successfully');

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('admin/blog/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/deletePost{id}", methods="POST", name="admin_post_delete")
     *
     */
    public function delete(Request $request, Post $post): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $this->addFlash('info', 'Erreur Jeton CSRF lors de la suppression');
            return $this->redirectToRoute('admin_post_index');
        }

        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $post->getTags()->clear();

        $this->em->remove($post);
        $this->em->flush();

        $this->addFlash('success', 'post.deleted_successfully');

        return $this->redirectToRoute('admin_post_index');
    }

    /**
     * @Route("/showWork{work}-{read}", name="admin_showWork")
     */
    public function showWork(Works $work, $read = null, Request $request, EventDispatcherInterface $eventDispatcher)
    {
        if ($read !== null) {
            if ($work->getIsRead() != 1) { // Vérification pour prévenir les renvois d'infos
                $work->setIsRead(1);

                $this->em->flush();

                $eventDispatcher->dispatch(new WorkReadEvent($work));

                $this->addFlash('success', 'Travaux marqués comme lu !');
                return $this->redirectToRoute('admin_showWork', ['work' => $work->getId()]);
            }
        }

        $notifs = $work->getNotifications();

        foreach ($notifs as $notif) {
            foreach ($notif->getUser() as $userNotif) {
                if ($userNotif == $this->getUser()) {
                    $notif->setIsRead(1);
                    $this->em->flush();
                }
            }
        }

        $comment = new Comments();

        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form['comment']->getData() !== null) {
                $user = $this->getUser();

                $comment->setUser($user);
                $comment->setPostAt(new \Datetime());
                $comment->setComment($form['comment']->getData());
                $this->em->persist($comment);

                $work->addComment($comment);

                if ($work->getIsRead() != 1) {
                    $work->setIsRead(1);
                    $eventDispatcher->dispatch(new WorkReadEvent($work));
                }

                $this->em->flush();
            }

            $eventDispatcher->dispatch(new NewMessageWorkEvent($work));


            $this->addFlash('success', 'Commentaire envoyé !');
            return $this->redirectToRoute('admin_showWork', ['work' => $work->getId()]);
        }


        return $this->render('admin/blog/showWork.html.twig', [
            'work' => $work,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editUser/{user}", methods="GET|POST", name="admin_userEdit")
     * @isGranted("ROLE_ADMIN")
     */
    public function userEdit(User $user, Request $request): Response
    {

        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (in_array('ROLE_STUDENT', $form['roles']->getData()) AND $form['classroom']->getData()->getId() == 99 OR $form['classroom']->getData()->getId() == 100 OR $form['classroom']->getData()->getId() == 110 OR $form['classroom']->getData()->getId() == 120 OR $form['classroom']->getData()->getId() == 130 OR $form['classroom']->getData()->getId() == 140) {
                $this->addFlash('danger', 'Vous ne pouvez pas choisir ce groupe d\'élèves ! ('. $form['classroom']->getData()->getName() .')');
                return $this->redirectToRoute('admin_index');
            }

            if ($form['plainPassword']->getData() !== null ) {
                $encodedPassword = $this->passwordEncoder->encodePassword($user, $form['plainPassword']->getData());
                $user->setPassword($encodedPassword);
            }

            if (in_array('ROLE_STUDENT', $form['roles']->getData())) {
                $roles['Role'] = 'ROLE_STUDENT';
                $user->setRoles($roles);
            } elseif (in_array('ROLE_TEACHER', $form['roles']->getData())) {
                $roles['Role'] = 'ROLE_TEACHER';
                $user->setRoles($roles);
            } else {
                $roles['Role'] = 'ROLE_USER';
                $user->setRoles($roles);
            }

            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                $user->setClassroom(null);
            }

            $this->em->flush();

            $this->addFlash('success', 'Utilisateur '. $form['firstname']->getData() .' '. $form['name']->getData() .' modifié !');

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/blog/editUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/deleteUser/{user}", methods="POST", name="admin_userDelete")
     * @isGranted("ROLE_ADMIN")
     *
     */
    public function userDelete(Request $request, User $user): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $this->addFlash('info', 'Erreur Jeton CSRF lors de la suppression');
            return $this->redirectToRoute('admin_index');
        }

        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('success', 'Utilisateur '. $user->getFirstname() .' '. $user->getName() .' supprimé !');

        return $this->redirectToRoute('admin_index');
    }

}
