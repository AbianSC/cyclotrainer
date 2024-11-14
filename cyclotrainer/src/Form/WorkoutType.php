<?php

namespace App\Form;

use App\Entity\Workout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class WorkoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Workout Name',
            ])
            ->add('workoutExercises', CollectionType::class, [
                'entry_type' => WorkoutExerciseType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true, // This line enables the prototype for dynamic forms
                'prototype_name' => '__name', // Placeholder name used by JavaScript
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Workout::class,
        ]);
    }
}
