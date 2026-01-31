<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifRepository::class)]
class Tarif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $jourSemaine = null;

    #[ORM\Column]
    private ?float $coefficient = null;

    #[ORM\ManyToOne(inversedBy: 'tarifs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Film $film = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJourSemaine(): ?string
    {
        return $this->jourSemaine;
    }

    public function setJourSemaine(string $j): static
    {
        $this->jourSemaine = $j;

        return $this;
    }

    public function getCoefficient(): ?float
    {
        return $this->coefficient;
    }

    public function setCoefficient(float $c): static
    {
        $this->coefficient = $c;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }
}
