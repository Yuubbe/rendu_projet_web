<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use App\Service\RecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FilmRepository $filmRepository, RecommendationService $recommendationService): Response
    {
        $films = $filmRepository->findBy([], ['id' => 'DESC'], 10);
        $user = $this->getUser();

        $recommendations = [];
        if ($user instanceof \App\Entity\User) {
            $recommendations = $recommendationService->recommend($user, 6);
        }

        return $this->render('home/index.html.twig', [
            'films' => $films,
            'recommendations' => $recommendations,
        ]);
    }
}
