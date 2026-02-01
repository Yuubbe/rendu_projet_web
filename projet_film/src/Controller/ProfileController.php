<?php

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        $locations = $user->getLocations();

        // Gestion changement de mot de passe
        $error = null;

        if ($request->isMethod('POST')) {
            $current = $request->request->get('current_password');
            $new = $request->request->get('new_password');
            $confirm = $request->request->get('confirm_password');

            if (!$current || !$new || !$confirm) {
                $error = 'Merci de remplir tous les champs.';
            } elseif ($new !== $confirm) {
                $error = 'La confirmation ne correspond pas.';
            } elseif (!$passwordHasher->isPasswordValid($user, $current)) {
                $error = 'Mot de passe actuel invalide.';
            } else {
                $hashed = $passwordHasher->hashPassword($user, $new);
                $user->setPassword($hashed);
                $em->flush();
                $this->addFlash('success', 'Mot de passe mis à jour.');
                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'locations' => $locations,
            'error' => $error,
        ]);
    }
}
