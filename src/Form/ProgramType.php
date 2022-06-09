<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cp_program1', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CP d\'Isabelle',
            ])
            ->add('cp_program2', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CP de Pascal et Véronique',
            ])
            ->add('ce1_program1', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CE1 d\'Isabelle',
            ])
            ->add('ce1_program2', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CE1 de Pascal et Véronique',
            ])
            ->add('ce1_program3', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CE1 d\'Hélène',
            ])
            ->add('ce2_program1', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CE2 d\'Hélène',
            ])
            ->add('ce2_program2', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CE2 de Syverine',
            ])
            ->add('cm1_program1', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CM1 de Syverine',
            ])
            ->add('cm1_program2', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CM1 de Delphine',
            ])
            ->add('cm2_program1', FileType::class, [
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
                'mapped' => false,
                'required' => false,
                'label' => 'Programme des CM2 d\'Eric',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Programs::class,
        ]);
    }
}
