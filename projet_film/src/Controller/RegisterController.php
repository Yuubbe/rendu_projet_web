<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $user = new User();

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');

            if ($email && $plainPassword) {
                $user->setEmail($email);
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig');
    }
}
