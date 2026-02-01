<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Repository\GenreRepository;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class FilmController extends AbstractController
{
    #[Route('/films', name: 'app_film_index')]
    public function index(Request $request, FilmRepository $filmRepository, GenreRepository $genreRepository): Response
    {
        $genreId = $request->query->getInt('genre', 0);
        $annee = $request->query->getInt('annee', 0);
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 9;

        $qb = $filmRepository->createFilteredQueryBuilder($genreId ?: null, $annee ?: null);

        $query = $qb->getQuery();
        $total = count($query->getResult());

        $films = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalPages = max(1, (int) ceil($total / $limit));

        return $this->render('film/index.html.twig', [
            'films' => $films,
            'genres' => $genreRepository->findAll(),
            'current_genre' => $genreId,
            'current_annee' => $annee ?: '',
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }

    #[Route('/films/search', name: 'app_film_search', methods: ['GET'])]
    public function search(Request $request, FilmRepository $filmRepository): JsonResponse
    {
        $term = trim((string) $request->query->get('q', ''));
        $genreParam = (string) $request->query->get('genres', '');
        $genreIds = array_filter(array_map('intval', $genreParam !== '' ? explode(',', $genreParam) : []));
        $anneeMin = $request->query->getInt('annee_min', 0) ?: null;
        $anneeMax = $request->query->getInt('annee_max', 0) ?: null;

        $results = $filmRepository->searchAdvanced($term ?: null, $genreIds, $anneeMin, $anneeMax, 20);
        $suggestions = $filmRepository->suggestTitles($term ?: null, 5);

        return $this->json([
            'results' => array_map(static function (array $row) {
                return [
                    'id' => $row['id'],
                    'titre' => $row['titre'],
                    'annee' => $row['annee'] ?? null,
                    'prix' => $row['prixLocationDefaut'] ?? null,
                    'affiche' => $row['affiche'] ?? null,
                ];
            }, $results),
            'suggestions' => $suggestions,
        ]);
    }

    #[Route('/film/{id}', name: 'app_film_show', requirements: ['id' => '\\d+'])]
    public function show(Film $film): Response
    {
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/film/new', name: 'app_film_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($film);
            $em->flush();

            return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
        }

        return $this->render('film/form.html.twig', [
            'form' => $form,
            'is_edit' => false,
        ]);
    }

    #[Route('/film/{id}/edit', name: 'app_film_edit', requirements: ['id' => '\\d+'])]
    public function edit(Film $film, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
        }

        return $this->render('film/form.html.twig', [
            'form' => $form,
            'is_edit' => true,
        ]);
    }

    #[Route('/film/{id}/delete', name: 'app_film_delete', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function delete(Film $film, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete_film_'.$film->getId(), $request->request->get('_token'))) {
            $em->remove($film);
            $em->flush();
        }

        return $this->redirectToRoute('app_film_index');
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
