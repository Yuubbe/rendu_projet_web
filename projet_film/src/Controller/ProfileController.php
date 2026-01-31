<?php

namespace App\Controller;

use App\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        $locations = $user->getLocations();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'locations' => $locations,
        ]);
    }
}
