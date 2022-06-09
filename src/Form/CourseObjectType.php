<?php

namespace App\Form;

use App\Entity\CourseObject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class CourseObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'objet',
                'help' => 'Ex: Vidéos, exercices, corrigés...'
            ])
            ->add('content', CKEditorType::class, [
                'config_name' => 'ck_config',
            ])
            ->add('url')
            ->add('upload', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CourseObject::class,
        ]);
    }
}
