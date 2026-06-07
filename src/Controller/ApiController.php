<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/status', name: 'api_status', methods: ['GET'])]
    public function getStatus(): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'message' => 'L\'API Symfony de TankRent répond parfaitement !'
        ]);
    }
}