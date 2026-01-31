<?php

namespace App\Entity;

use App\Repository\DetailLocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailLocationRepository::class)]
class DetailLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $PrixJour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixJour(): ?float
    {
        return $this->PrixJour;
    }

    public function setPrixJour(float $PrixJour): static
    {
        $this->PrixJour = $PrixJour;

        return $this;
    }
}
