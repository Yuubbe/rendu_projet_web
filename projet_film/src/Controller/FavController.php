<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FavController extends AbstractController
{
    #[Route('/fav', name: 'app_fav')]
    public function index(): Response
    {
        return $this->render('fav/index.html.twig', [
            'controller_name' => 'FavController',
        ]);
    }
}
