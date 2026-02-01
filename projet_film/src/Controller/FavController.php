<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FavController extends AbstractController
{
    #[Route('/fav', name: 'app_fav')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        $favoris = $user->getFavoris();

        return $this->render('fav/index.html.twig', [
            'favoris' => $favoris,
        ]);
    }

    #[Route('/film/{id}/favori/toggle', name: 'app_film_favori_toggle', requirements: ['id' => '\\d+'])]
    public function toggleFavori(Film $film, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        if ($user->getFavoris()->contains($film)) {
            $user->removeFavori($film);
        } else {
            $user->addFavori($film);
        }

        $em->flush();

        return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
    }

    #[Route('/fav/export/{format}', name: 'app_fav_export', defaults: ['format' => 'csv'], requirements: ['format' => 'csv|json'])]
    public function export(string $format = 'csv'): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        $favoris = $user->getFavoris();

        if ($format === 'json') {
            $data = [];
            foreach ($favoris as $film) {
                $data[] = [
                    'id' => $film->getId(),
                    'titre' => $film->getTitre(),
                    'annee' => $film->getAnnee(),
                ];
            }

            return $this->json($data);
        }

        $lines = ["id,titre,annee"];
        foreach ($favoris as $film) {
            $lines[] = sprintf('%d,"%s",%d', $film->getId(), str_replace('"', '""', $film->getTitre()), $film->getAnnee());
        }
        $content = implode("\n", $lines);

        return new Response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="favoris.csv"',
        ]);
    }

    #[Route('/fav/import', name: 'app_fav_import', methods: ['POST'])]
    public function import(Request $request, FilmRepository $filmRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $payload = json_decode($request->getContent(), true);
        $ids = is_array($payload) ? $payload : ($payload['ids'] ?? []);
        if (!is_array($ids)) {
            return $this->json(['error' => 'Format JSON invalide'], 400);
        }

        $added = 0;
        foreach ($ids as $id) {
            $film = $filmRepository->find((int) $id);
            if ($film) {
                /** @var \App\Entity\User $user */
                if (!$user->getFavoris()->contains($film)) {
                    $user->addFavori($film);
                    $added++;
                }
            }
        }

        $em->flush();

        return $this->json([
            'status' => 'ok',
            'added' => $added,
        ]);
    }
}
