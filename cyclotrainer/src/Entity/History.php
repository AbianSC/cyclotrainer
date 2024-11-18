<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HistoryRepository;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)] 
    private ?\DateTime $dateWorkout = null;

    #[ORM\ManyToOne(inversedBy: 'history')]
    private ?Workout $workout = null;

    #[ORM\ManyToOne(inversedBy: 'histories')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    { 
        $this->duration = $duration;

        return $this;
    }

    public function getDateWorkout(): ?\DateTime
    {
        return $this->dateWorkout;
    }

    public function setDateWorkout(\DateTime $dateWorkout): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
