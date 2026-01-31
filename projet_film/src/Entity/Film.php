<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\FilmRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 255)]
    private ?string $synopsis = null;

    #[ORM\Column]
    private ?int $prixLocationDefaut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPrixLocationDefaut(): ?int
    {
        return $this->prixLocationDefaut;
    }

    public function setPrixLocationDefaut(int $prixLocationDefaut): static
    {
        $this->prixLocationDefaut = $prixLocationDefaut;

        return $this;
    }
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'films')]
    private Collection $genres;



    #[ORM\OneToMany(mappedBy: 'film', targetEntity: DetailLocation::class)]
    private Collection $detailLocations;


    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoris')]
    private Collection $utilisateursFavoris;


    #[ORM\OneToMany(mappedBy: 'film', targetEntity: Tarif::class)]
    private Collection $tarifs;


    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->detailLocations = new ArrayCollection();
        $this->utilisateursFavoris = new ArrayCollection();
        $this->tarifs = new ArrayCollection();
    }
}