<?php

namespace App\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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
}
