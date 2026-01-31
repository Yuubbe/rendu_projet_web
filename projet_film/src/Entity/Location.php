<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateLocation = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private ?string $locationPrixFinal = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: DetailLocation::class, cascade: ['persist'])]
    private Collection $detailLocations;

    public function __construct()
    {
        $this->detailLocations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLocation(): ?\DateTimeInterface
    {
        return $this->dateLocation;
    }

    public function setDateLocation(\DateTimeInterface $dateLocation): static
    {
        $this->dateLocation = $dateLocation;

        return $this;
    }

    public function getLocationPrixFinal(): ?string
    {
        return $this->locationPrixFinal;
    }

    public function setLocationPrixFinal(string $locationPrixFinal): static
    {
        $this->locationPrixFinal = $locationPrixFinal;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, DetailLocation>
     */
    public function getDetailLocations(): Collection
    {
        return $this->detailLocations;
    }
}
