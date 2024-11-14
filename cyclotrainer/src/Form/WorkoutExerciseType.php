<?php

namespace App\Form;

use App\Entity\Exercise;
use App\Entity\WorkoutExercise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class WorkoutExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reps', IntegerType::class)
            ->add('weight', NumberType::class)
            ->add('sets', IntegerType::class)
            ->add('exercise', EntityType::class, [
                'class' => Exercise::class,
                'choice_label' => 'name',
                'label' => 'Exercise',
                'placeholder' => 'Choose an exercise',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkoutExercise::class,
        ]);
    }
}


            
