<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationApiController extends AbstractController
{
    #[Route('/api/reservations/new', name: 'api_reservation_create', methods: ['POST', 'OPTIONS'])]
    public function create(Request $request, VehiculeRepository $vehiculeRepo, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            return $response;
        }

        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['tankId'], $data['dateDebut'], $data['dateFin'])) {
            $response = $this->json(['error' => 'Incomplet'], Response::HTTP_BAD_REQUEST);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        $vehicule = $vehiculeRepo->find($data['tankId']);
        if (!$vehicule) {
            $response = $this->json(['error' => 'Introuvable'], Response::HTTP_NOT_FOUND);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        $debut = new \DateTime($data['dateDebut']);
        $fin = new \DateTime($data['dateFin']);

        $interval = $debut->diff($fin);
        $prixTotal = 1000 * ($interval->days + 1);

        $reservation = new Reservation();
        $reservation->setDateDebut($debut);
        $reservation->setDateFin($fin);
        $reservation->setPrix($prixTotal);
        $reservation->setIdVehicule($vehicule);
        
        if (method_exists($vehicule, 'setEtat')) {
            $vehicule->setEtat('Réservé');
        }

        $userRepository = $em->getRepository(User::class);
        $user = null;

        if (isset($data['userId']) && !empty($data['userId'])) {
            $user = $userRepository->find($data['userId']);
        }

        // Repli de sécurité si jamais le payload est vide (évite un crash SQL Not Null)
        if (!$user) {
            $user = $userRepository->find(3) ?? $userRepository->findOneBy([]);
        }

        if ($user) {
            $reservation->setIdUser($user);
        }

        $em->persist($reservation);
        $em->flush();

        $response = $this->json(['success' => true, 'message' => 'Réservation enregistrée avec succès !']);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}