<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $duration = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateWorkout = null;

    #[ORM\ManyToOne(inversedBy: 'history')]
    private ?Workout $workout = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?\DateTimeImmutable
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeImmutable $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateWorkout(): ?\DateTimeImmutable
    {
        return $this->dateWorkout;
    }

    public function setDateWorkout(\DateTimeImmutable $dateWorkout): static
    {
        $this->dateWorkout = $dateWorkout;

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
}
