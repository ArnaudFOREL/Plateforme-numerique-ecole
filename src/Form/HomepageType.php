<?php

namespace App\Form;

use App\Entity\Homepage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class HomepageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', CKEditorType::class, [
                'config_name' => 'ck_mini',
            ])
            // ->add('cpProgram', FileType::class, [
            //     'attr' => [
            //         'class' => 'custom-file-input',
            //         'lang' => 'fr'
            //     ],
            //     'mapped' => false,
            //     'required' => false,
            // ])
            // ->add('ce1Program', FileType::class, [
            //     'attr' => [
            //         'class' => 'custom-file-input',
            //         'lang' => 'fr'
            //     ],
            //     'mapped' => false,
            //     'required' => false,
            // ])
            // ->add('imageCP', FileType::class, [
            //     'required' => false,
            //     'mapped' => false
            // ])
            // ->add('imageCE1', FileType::class, [
            //     'required' => false,
            //     'mapped' => false
            // ])
            ->add('pageImage', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Homepage::class,
        ]);
    }
}
