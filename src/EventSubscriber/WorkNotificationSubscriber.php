<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Entity\Works;
use App\Entity\Notifications;
use App\Repository\UserRepository;
use App\Repository\NotificationsRepository;
use App\Repository\ClassroomRepository;
use App\Events\WorkCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Notifies post's author about new comments.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class WorkNotificationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $translator;
    private $urlGenerator;
    private $manager;
    private $userrepository;
    private $notifrepository;
    private $classrepository;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, UserRepository $userrepository, NotificationsRepository $notifrepository, ClassroomRepository $classrepository)
    {
        $this->em = $manager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->userrepository = $userrepository;
        $this->notifrepository = $notifrepository;
        $this->classrepository = $classrepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkCreatedEvent::class => 'onWorkCreated',
        ];
    }

    public function onWorkCreated(WorkCreatedEvent $event): void
    {
        /** @var Works $work */
        $work = $event->getWork();
        $type = $event->getType();

        $teachers = $this->userrepository->findTeachers();
        $class = $work->getUser()->getClassroom();

        $wordsClass = str_word_count($class->getName(), 1, 'àáãç3');

        foreach ($teachers as $teacher) {
            
            foreach ($wordsClass as $word) {
                if (preg_match("/". $word ."/i", $teacher->getFirstName())) {

                    $notif = new Notifications();

                    $notif->addUser($teacher);
                    $notif->addWork($work);
                    $notif->setPostAt(new \Datetime);
                    $notif->setIsRead(0);
                    $notif->setType($type);

                    $this->em->persist($notif);

                    $today = new \Datetime();

                    $linkToWork = $this->urlGenerator->generate(
                        'admin_showWork', 
                        ['work' => $work->getId() ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $subject = "Nouveau travail de ".$work->getUser()->getFirstName()." ".$work->getUser()->getName()." le ". $today->format('d/m/Y');
                    $body = 'Vous avez reçu un nouveau travail à valider de '.$work->getUser()->getFirstName()." ".$work->getUser()->getName().'.<br/> Le sujet de ce travail est "'.$work->getSubject().'". Vous pouvez le consulter en suivant <a href="'.$linkToWork.'">ce lien</a>.';

                    // See https://symfony.com/doc/current/mailer.html
                    $email = (new Email())
                        ->from("travaux-eleves@ecole-ayse.fr")
                        ->to($teacher->getEmail())
                        ->subject($subject)
                        ->html($body)
                    ;

                    // In config/packages/dev/mailer.yaml the delivery of messages is disabled.
                    // That's why in the development environment you won't actually receive any email.
                    // However, you can inspect the contents of those unsent emails using the debug toolbar.
                    $this->mailer->send($email);
                } 

            }  

        }
    
        $this->em->flush();

    }
}
