<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $DateLocation = null;

    #[ORM\Column]
    private ?float $LocationPrixFinal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLocation(): ?\DateTime
    {
        return $this->DateLocation;
    }

    public function setDateLocation(\DateTime $DateLocation): static
    {
        $this->DateLocation = $DateLocation;

        return $this;
    }

    public function getLocationPrixFinal(): ?float
    {
        return $this->LocationPrixFinal;
    }

    public function setLocationPrixFinal(float $LocationPrixFinal): static
    {
        $this->LocationPrixFinal = $LocationPrixFinal;

        return $this;
    }

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private User $utilisateur;


    #[ORM\OneToMany(mappedBy: 'location', targetEntity: DetailLocation::class, cascade: ['persist'])]
    private Collection $detailLocations;

    public function __construct()
    {
        $this->detailLocations = new ArrayCollection();
    }
}
