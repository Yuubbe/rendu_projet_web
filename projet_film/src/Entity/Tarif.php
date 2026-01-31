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

    #[ORM\Column(length: 255)]
    private ?string $JourSemaine = null;

    #[ORM\Column]
    private ?float $Coefficient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJourSemaine(): ?string
    {
        return $this->JourSemaine;
    }

    public function setJourSemaine(string $JourSemaine): static
    {
        $this->JourSemaine = $JourSemaine;

        return $this;
    }

    public function getCoefficient(): ?float
    {
        return $this->Coefficient;
    }

    public function setCoefficient(float $Coefficient): static
    {
        $this->Coefficient = $Coefficient;

        return $this;
    }
}
