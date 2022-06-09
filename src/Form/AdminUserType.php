<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom utilisateur',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices'  => [
                    'Elève' => "ROLE_STUDENT",
                    'Professeur' => "ROLE_TEACHER",
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez une valeur uniquement pour modifier sinon laissez vide'
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('classroom', EntityType::class, [
                'label' => 'Groupe',
                'class' => Classroom::class,
                'choice_label' => 'name',
                'choice_attr' => function($choice, $key, $value) {
                    if ($value >= 99) {
                        return ['disabled' => 'disabled'];
                    }
                    return ['class' => 'custom-select'];
                },
            ])
        ;

        //roles field data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    if (!is_null($rolesArray) AND !array_key_first($rolesArray) == 'Role') {
                        return $rolesArray[0];
                    } else {
                        return $rolesArray['Role'];
                    }
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
    
            // checks if the User exists and if the User is a Teacher
            // If no data is passed to the form, the data is "null".
            if ($user || null != $user->getId() AND in_array('ROLE_TEACHER', $user->getRoles())) {
                $form->add('email', EmailType::class, [
                    'label' => 'Email',
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]);
            } else { // The user is new or a student
                $form->add('email', EmailType::class, [
                    'label' => 'Email',
                    'attr' => [
                        'class' => 'form-control',
                        'disabled' => 'disabled',
                        'placeholder' => 'Ce champ n\'est pas disponible pour les élèves',
                    ],
                    'required' => false,
                    'mapped' => false
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
