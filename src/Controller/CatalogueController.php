<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
//     #[Route('/catalogue', name: 'app_catalogue')]
//     public function index(VehiculeRepository $vehiculeRepository): Response
//     {
//         return $this->render('catalogue/index.html.twig', [
//             'controller_name' => 'CatalogueController',
//             'vehicules' => $vehiculeRepository->findAll(),
//         ]);
//     }
// }

    #[Route('/api/vehicules', name: 'api_vehicules', methods: ['GET'])]
    public function index(VehiculeRepository $vehiculeRepository): JsonResponse
    {
        $vehiculesdb = $vehiculeRepository->findAll();
        $data = [];

        foreach ($vehiculesdb as $vehicule) {
            $data[] = [
                'id' => $vehicule->getId(),
                'nom' => $vehicule->getNom(),
                'type' => $vehicule->getType(),
                'image' => $vehicule->getImage(),
            ];
        }

        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}