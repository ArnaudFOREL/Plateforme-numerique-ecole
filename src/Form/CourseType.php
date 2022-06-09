<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Category;
use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('startAt', DateTimePickerType::class, [
                'attr' => [
                    'class' => "form-control datepicker",
                    'data-value' => $options['start']
                ]
            ])
            ->add('endAt', DateTimePickerType::class, [
                'attr' => [
                    'class' => "form-control datepicker",
                    'data-value' => $options['end']
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr'
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'choice_attr' => function($choice, $key, $value) {
                    return [
                        'class' => 'rounded-circle',
                        'data-icon' => '/'.$choice->getImage()
                    ];
                },
            ])
            ->add('classroom', EntityType::class, [
                'class' => Classroom::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'mdb-select md-form'
                ]
            ])
            ->add('is_uploadEnd', ChoiceType::class, [
                'choices'  => [
                    'Non' => false,
                    'Oui' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
            'start' => date('Y-m-d'),
            'end' => date('Y-m-d')
        ]);
    }
}
