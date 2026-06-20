<?php
namespace App\Controller\Api;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class VehiculeApiController extends AbstractController
{
    public function __construct(private LogService $logService) {}
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
                'id'          => $vehicule->getId(),
                'nom'         => $vehicule->getNom(),
                'pays'        => $vehicule->getPays(),
                'type'        => $vehicule->getType(),
                'masse'       => $vehicule->getMasse(),
                'image'       => $vehicule->getImage(),
                'description' => $vehicule->getDescription(),
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
            'id'          => $vehicule->getId(),
            'nom'         => $vehicule->getNom(),
            'pays'        => $vehicule->getPays(),
            'type'        => $vehicule->getType(),
            'masse'       => $vehicule->getMasse(),
            'image'       => $vehicule->getImage(),
            'description' => $vehicule->getDescription(),
        ];

        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    #[Route('/api/admin/vehicules/{id}', name: 'api_admin_vehicules_update', methods: ['PUT', 'OPTIONS'])]
    public function update(?Vehicule $vehicule, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('PUT');
        }
        if (!$vehicule) {
            return $this->corsJson(['error' => 'Introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true) ?? [];

        if (isset($data['nom']))         $vehicule->setNom($data['nom']);
        if (isset($data['pays']))        $vehicule->setPays($data['pays']);
        if (isset($data['type']))        $vehicule->setType($data['type']);
        if (isset($data['masse']))       $vehicule->setMasse((int) $data['masse']);
        if (isset($data['description'])) $vehicule->setDescription(mb_substr($data['description'], 0, 255));
        if (isset($data['image']))       $vehicule->setImage($data['image']);

        $em->flush();

        $logUserId    = (int) $request->headers->get('X-User-Id') ?: null;
        $logUserEmail = $request->headers->get('X-User-Email');
        $this->logService->log('UPDATE_VEHICLE', $logUserId, $logUserEmail, ['id' => $vehicule->getId(), 'nom' => $vehicule->getNom()]);

        return $this->corsJson(['success' => true, 'id' => $vehicule->getId()]);
    }

    #[Route('/api/admin/vehicules/{id}', name: 'api_admin_vehicules_delete', methods: ['DELETE', 'OPTIONS'])]
    public function delete(?Vehicule $vehicule, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('DELETE');
        }
        if (!$vehicule) {
            return $this->corsJson(['error' => 'Introuvable'], 404);
        }

        $nom = $vehicule->getNom();
        $id  = $vehicule->getId();

        $em->remove($vehicule);
        $em->flush();

        $logUserId    = (int) $request->headers->get('X-User-Id') ?: null;
        $logUserEmail = $request->headers->get('X-User-Email');
        $this->logService->log('DELETE_VEHICLE', $logUserId, $logUserEmail, ['id' => $id, 'nom' => $nom]);

        return $this->corsJson(['success' => true]);
    }

    private function corsJson(array $data, int $status = 200): JsonResponse
    {
        $r = $this->json($data, $status);
        $r->headers->set('Access-Control-Allow-Origin', '*');
        return $r;
    }

    private function corsOptions(string $methods): JsonResponse
    {
        $r = new JsonResponse(null, 204);
        $r->headers->set('Access-Control-Allow-Origin', '*');
        $r->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-User-Id, X-User-Email');
        $r->headers->set('Access-Control-Allow-Methods', $methods . ', OPTIONS');
        return $r;
    }
}