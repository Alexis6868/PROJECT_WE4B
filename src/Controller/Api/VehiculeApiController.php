<?php
namespace App\Controller\Api;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class VehiculeApiController extends AbstractController
{
    #[Route('/api/vehicules', name: 'api_vehicules_list', methods: ['GET'])]
    public function list(VehiculeRepository $repo): JsonResponse
    {
        $vehicules = $repo->findAll();
        $data = [];

        foreach ($vehicules as $vehicule) {
            $data[] = [
                'id' => $vehicule->getId(),
                'nom' => $vehicule->getNom(),
                'type' => $vehicule->getType(),
                'image' => $vehicule->getImage(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/vehicules/{id}', name: 'api_vehicules_show', methods: ['GET'])]
    public function show(?Vehicule $vehicule): JsonResponse
    {
        if (!$vehicule) {
            return new JsonResponse(['error' => 'Vehicule not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $vehicule->getId(),
            'nom' => $vehicule->getNom(),
            'type' => $vehicule->getType(),
            'image' => $vehicule->getImage(),
        ];

        return new JsonResponse($data);
    }
}