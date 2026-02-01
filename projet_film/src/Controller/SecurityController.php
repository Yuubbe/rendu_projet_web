<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }



    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email', '');
            $plainPassword = (string) $request->request->get('password', '');

            if ($email !== '' && $plainPassword !== '') {
                $user = new User();
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

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository): Response
    {
        $emailSent = false;

        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email', '');

            if ($email !== '') {
                // Optionnel: vérifier si l'utilisateur existe
                $userRepository->findOneBy(['email' => $email]);
                $emailSent = true;
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'email_sent' => $emailSent,
        ]);
    }
}

