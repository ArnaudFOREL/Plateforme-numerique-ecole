<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class ClassroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($choice, $key, $value) {
                    return $choice->getName(). ' '. $choice->getFirstname();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'group_by' => function($choice, $key, $value) {
                    if (in_array('ROLE_ADMIN', $choice->getRoles())) {
                        return 'Administrateurs';
                    } elseif (in_array('ROLE_TEACHER', $choice->getRoles())) {
                        return "Professeurs";
                    }

                    return 'Elèves';
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'mdb-select md-form'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Classroom::class,
        ]);
    }
}
