<?php

namespace App\Entity;

use App\Repository\WorkoutExerciseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkoutExerciseRepository::class)]
class WorkoutExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $reps = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $weight = null;

    #[ORM\Column]
    private ?int $sets = null;

    #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
    #[ORM\JoinColumn(name: 'workout_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Workout $workout = null;

    #[ORM\ManyToOne(inversedBy: 'workoutExercises')]
    #[ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id')]
    private ?Exercise $exercise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReps(): ?int
    {
        return $this->reps;
    }

    public function setReps(int $reps): static
    {
        $this->reps = $reps;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getSets(): ?int
    {
        return $this->sets;
    }

    public function setSets(int $sets): static
    {
        $this->sets = $sets;

        return $this;
    }

    public function getWorkout(): ?Workout
    {
        return $this->workout;
    }

    public function setWorkout(?Workout $workout): static
    {
        $this->workout = $workout;

        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;

        return $this;
    }
}
