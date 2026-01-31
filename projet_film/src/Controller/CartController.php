<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Location;
use App\Entity\DetailLocation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    private const CART_KEY = 'cart_items';

    #[Route('/panier', name: 'app_cart_show')]
    public function show(Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get(self::CART_KEY, []);

        $total = 0.0;
        foreach ($cart as $item) {
            $total += $item['prix'];
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cart,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_cart_add', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function add(Film $film, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $cart = $session->get(self::CART_KEY, []);

        $jour = $request->request->get('jour', 'lundi');
        $prix = $film->getPrixPourJour($jour);

        $cart[] = [
            'film_id' => $film->getId(),
            'titre' => $film->getTitre(),
            'jour' => $jour,
            'prix' => $prix,
        ];

        $session->set(self::CART_KEY, $cart);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/panier/supprimer/{index}', name: 'app_cart_remove', requirements: ['index' => '\\d+'], methods: ['POST'])]
    public function remove(int $index, Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $cart = $session->get(self::CART_KEY, []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            $session->set(self::CART_KEY, $cart);
        }

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/panier/vider', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove(self::CART_KEY);

        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/panier/confirmer', name: 'app_cart_confirm', methods: ['POST'])]
    public function confirm(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $cart = $session->get(self::CART_KEY, []);

        if (empty($cart)) {
            return $this->redirectToRoute('app_cart_show');
        }

        $location = new Location();
        $location->setUtilisateur($user);
        $location->setDateLocation(new \DateTimeImmutable());

        $total = 0.0;

        foreach ($cart as $item) {
            $film = $em->getRepository(Film::class)->find($item['film_id']);
            if (!$film) {
                continue;
            }

            $detail = new DetailLocation();
            $detail->setFilm($film);
            $detail->setPrixJour($item['prix']);
            $detail->setLocation($location);

            $em->persist($detail);
            $total += $item['prix'];
        }

        $location->setLocationPrixFinal($total);
        $em->persist($location);
        $em->flush();

        $session->remove(self::CART_KEY);

        return $this->redirectToRoute('app_profile');
    }
}
