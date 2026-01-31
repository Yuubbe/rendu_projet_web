<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(name: 'prix_location_defaut')]
    private ?int $prixLocationDefaut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $affiche = null;

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

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(?string $affiche): static
    {
        $this->affiche = $affiche;

        return $this;
    }
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'films')]
    #[ORM\JoinTable(name: 'film_genre')]
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

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUtilisateursFavoris(): Collection
    {
        return $this->utilisateursFavoris;
    }

    /**
     * Retourne le Tarif pour un jour donné ("lundi", "mardi", ...), ou null si aucun.
     */
    public function getTarifPourJour(string $jour): ?Tarif
    {
        $jour = mb_strtolower($jour);

        foreach ($this->tarifs as $tarif) {
            if (mb_strtolower($tarif->getJourSemaine() ?? '') === $jour) {
                return $tarif;
            }
        }

        return null;
    }

    /**
     * Calcule le prix pour un jour donné en utilisant le Tarif associé.
     */
    public function getPrixPourJour(string $jour): float
    {
        $tarif = $this->getTarifPourJour($jour);

        if ($tarif === null || $tarif->getCoefficient() === null) {
            return (float) $this->prixLocationDefaut;
        }

        return (float) $this->prixLocationDefaut * (float) $tarif->getCoefficient();
    }
}