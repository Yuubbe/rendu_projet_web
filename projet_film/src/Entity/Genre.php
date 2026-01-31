<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $LibelleGenre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleGenre(): ?string
    {
        return $this->LibelleGenre;
    }

    public function setLibelleGenre(string $LibelleGenre): static
    {
        $this->LibelleGenre = $LibelleGenre;

        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Film::class, mappedBy: 'genres')]
    private Collection $films;


    public function __construct()
    {
        $this->films = new ArrayCollection();
    }
}
