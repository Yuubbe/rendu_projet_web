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

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }
    #[Route('/film/{id}/prix', name: 'app_film_prix', requirements: ['id' => '\d+'])]
    public function prix(Film $film, Request $request): JsonResponse
    {
        $jour = $request->query->get('jour', 'lundi');

        $prix = $film->getPrixPourJour($jour);

        return new JsonResponse([
            'jour' => $jour,
            'prix' => round($prix, 2),
        ]);
    }
}
