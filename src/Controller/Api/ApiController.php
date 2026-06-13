<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route; // 💡 Bien vérifier "Attribute" ici aussi

class ApiController extends AbstractController
{
    #[Route('/status', name: 'api_status', methods: ['GET'])]
    public function getStatus(): JsonResponse
    {
        $response = $this->json(['status' => 'OK', 'message' => 'Stack opérationnelle']);
        $response->headers->set('Access-Control-Allow-Origin', '*'); 
        return $response;
    }
}
