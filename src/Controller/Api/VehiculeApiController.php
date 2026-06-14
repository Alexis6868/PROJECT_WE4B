<?php
namespace App\Controller\Api;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Attribute\Route;

class VehiculeApiController extends AbstractController
{
    #[Route('/api/vehicules', name: 'api_vehicules_list', methods: ['GET', 'OPTIONS'])] // 👈 Ajout OPTIONS
    public function list(VehiculeRepository $repo, Request $request): JsonResponse
    {
        // 1. Gestion du Preflight CORS pour la liste
        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
            return $response;
        }

        $vehicules = $repo->findAll();
        $data = [];

        foreach ($vehicules as $vehicule) {
            $data[] = [
                'id'   => $vehicule->getId(),
                'nom'  => $vehicule->getNom(),
                'pays' => $vehicule->getPays(),
                'type' => $vehicule->getType(),
                'masse' => $vehicule->getMasse(),
                'image' => $vehicule->getImage(),
            ];
        }

        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*'); 
        return $response;
    }

    #[Route('/api/vehicules/{id}', name: 'api_vehicules_show', methods: ['GET', 'OPTIONS'])] // 👈 Ajout OPTIONS
    public function show(?Vehicule $vehicule, Request $request): JsonResponse
    {

        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
            return $response;
        }

        if (!$vehicule) {
            $response = new JsonResponse(['error' => 'Vehicule not found'], JsonResponse::HTTP_NOT_FOUND);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        $data = [
            'id' => $vehicule->getId(),
            'nom' => $vehicule->getNom(),
            'type' => $vehicule->getType(),
            'masse' => $vehicule->getMasse(),
            'image' => $vehicule->getImage(),
        ];

        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}