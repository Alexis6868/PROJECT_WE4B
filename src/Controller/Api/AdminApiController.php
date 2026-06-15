<?php

namespace App\Controller\Api;

use App\Entity\Vehicule;
use App\Repository\EntrepotRepository;
use App\Service\GeminiService;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminApiController extends AbstractController
{
    #[Route('/api/admin/recherche-ia', name: 'api_admin_recherche_ia', methods: ['POST', 'OPTIONS'])]
    public function rechercheIA(Request $request, GeminiService $geminiService): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsResponse();
        }

        $data = json_decode($request->getContent(), true);
        $nom  = trim($data['nom'] ?? '');

        if (!$nom) {
            return $this->jsonWithCors(['erreur' => 'Le nom du char est requis.'], 400);
        }

        $result = $geminiService->rechercherChar($nom);

        return $this->jsonWithCors($result);
    }

    #[Route('/api/admin/vehicules/import', name: 'api_admin_vehicules_import', methods: ['POST', 'OPTIONS'])]
    public function importVehicule(
        Request $request,
        EntityManagerInterface $em,
        EntrepotRepository $entrepotRepo,
        LogService $logService
    ): JsonResponse {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsResponse();
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['type'])) {
            return $this->jsonWithCors(['erreur' => 'Données incomplètes.'], 400);
        }

        $entrepot = $entrepotRepo->find(1);

        $vehicule = new Vehicule();
        $vehicule->setNom($data['nom']);
        $vehicule->setPays($data['pays'] ?? null);
        $vehicule->setType($data['type']);
        $vehicule->setDescription(mb_substr($data['description'] ?? '', 0, 255));
        $vehicule->setMasse((int) ($data['masse'] ?? 0));
        $vehicule->setImage($data['image'] ?? '');
        $vehicule->setEtat('Opérationnel');
        $vehicule->setIndiceMaintenance(80);
        $vehicule->setEntrepot($entrepot);

        $em->persist($vehicule);
        $em->flush();

        $logUserId    = (int) $request->headers->get('X-User-Id') ?: null;
        $logUserEmail = $request->headers->get('X-User-Email');
        $logService->log('CREATE_VEHICLE', $logUserId, $logUserEmail, [
            'id'  => $vehicule->getId(),
            'nom' => $vehicule->getNom(),
        ]);

        return $this->jsonWithCors([
            'success' => true,
            'id'      => $vehicule->getId(),
            'nom'     => $vehicule->getNom(),
        ], 201);
    }

    private function jsonWithCors(array $data, int $status = 200): JsonResponse
    {
        $response = $this->json($data, $status);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    private function corsResponse(): JsonResponse
    {
        $response = new JsonResponse(null, 204);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        return $response;
    }
}
