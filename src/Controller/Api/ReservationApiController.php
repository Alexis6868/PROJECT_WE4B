<?php
namespace App\Controller\Api;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ReservationApiController extends AbstractController
{
    #[Route('/api/reservations/new', name: 'api_reservation_create', methods: ['POST'])]
    public function create(Request $request, VehiculeRepository $vehiculeRepo, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    }
}