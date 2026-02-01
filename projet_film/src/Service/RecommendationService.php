<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\FilmRepository;

class RecommendationService
{
    public function __construct(private FilmRepository $filmRepository)
    {
    }

    /**
     * Recommandation simple avec exploration/exploitation (epsilon-greedy).
     *
     * @param User    $user          Utilisateur courant
     * @param int     $limit         Nombre de recommandations souhaité
     * @param bool|null $forceExplore Forcer l'exploration (tests) sinon stratégie epsilon-greedy
     *
     * @return array<\App\Entity\Film>
     */
    public function recommend(User $user, int $limit = 6, ?bool $forceExplore = null): array
    {
        $favorites = $user->getFavoris()->toArray();
        $favoriteIds = array_map(static fn($f) => $f->getId(), $favorites);

        // Collecter les genres favoris
        $genreCounts = [];
        foreach ($favorites as $film) {
            foreach ($film->getGenres() as $genre) {
                $genreCounts[$genre->getId()] = ($genreCounts[$genre->getId()] ?? 0) + 1;
            }
        }

        arsort($genreCounts);
        $topGenres = array_keys(array_slice($genreCounts, 0, 3, true));

    $epsilon = 0.2; // 20% exploration
    $explore = $forceExplore ?? ((mt_rand() / mt_getrandmax()) < $epsilon);

        $recommendations = [];

        if (!$explore && !empty($topGenres)) {
            $recommendations = $this->filmRepository->findByGenresPrioritized($topGenres, $favoriteIds, $limit);
        }

        if (empty($recommendations)) {
            $recommendations = $this->filmRepository->findMostPopular($limit, $favoriteIds);
        }

        // S'assure de ne pas dépasser la limite
        return array_slice($recommendations, 0, $limit);
    }
}
