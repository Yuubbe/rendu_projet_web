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

    #[ORM\Column(name: 'prix_jour', type: 'decimal', precision: 6, scale: 2)]
    private ?string $PrixJour = null;

    #[ORM\ManyToOne(inversedBy: 'detailLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Film $film = null;

    #[ORM\ManyToOne(inversedBy: 'detailLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixJour(): ?string
    {
        return $this->PrixJour;
    }

    public function setPrixJour(string $PrixJour): static
    {
        $this->PrixJour = $PrixJour;

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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }
}
