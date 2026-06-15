<?php

namespace App\Controller\Api;

use App\Service\LogService;
use App\Service\MongoService;
use App\Service\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class FileApiController extends AbstractController
{
    private const MAX_SIZE    = 5 * 1024 * 1024;
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];

    public function __construct(
        private UploadService $uploadService,
        private LogService $logService,
        private MongoService $mongo
    ) {}

    #[Route('/api/fichiers/upload', name: 'api_fichiers_upload', methods: ['POST', 'OPTIONS'])]
    public function upload(Request $request): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('POST');
        }

        $file = $request->files->get('file');
        if (!$file) {
            return $this->corsJson(['error' => 'Aucun fichier reçu.'], 400);
        }

        if ($file->getSize() > self::MAX_SIZE) {
            return $this->corsJson(['error' => 'Fichier trop volumineux (max 5 Mo).'], 400);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return $this->corsJson([
                'error' => 'Extension non autorisée. Formats acceptés : ' . implode(', ', self::ALLOWED_EXT),
            ], 400);
        }

        $rawUserId = $request->request->get('userId') ?? $request->headers->get('X-User-Id');
        $userId    = ($rawUserId !== null && $rawUserId !== '') ? (int) $rawUserId : null;
        $userEmail = $request->headers->get('X-User-Email');

        try {
            $doc = $this->uploadService->upload($file, $userId);
        } catch (\Exception $e) {
            return $this->corsJson(['error' => 'Erreur lors de l\'enregistrement du fichier.'], 500);
        }

        $this->logService->log('UPLOAD_FILE', $userId, $userEmail ?? null, [
            'originalName' => $doc['originalName'],
            'size'         => $doc['size'],
        ]);

        return $this->corsJson([
            'id'           => $doc['id'],
            'filename'     => $doc['filename'],
            'originalName' => $doc['originalName'],
            'mimeType'     => $doc['mimeType'],
            'size'         => $doc['size'],
        ], 201);
    }

    #[Route('/api/fichiers', name: 'api_fichiers_list', methods: ['GET', 'OPTIONS'])]
    public function list(Request $request): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('GET');
        }

        $filter  = [];
        $userIdParam = $request->query->get('userId');
        if ($userIdParam !== null) {
            $filter['userId'] = (int) $userIdParam;
        }

        $files = $this->mongo->find('fichier_metadata', $filter, [
            'sort' => ['uploadedAt' => -1],
        ]);

        return $this->corsJson($files);
    }

    #[Route('/api/fichiers/{id}/open', name: 'api_fichiers_open', methods: ['GET', 'OPTIONS'])]
    public function open(string $id, Request $request): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('GET');
        }

        $doc = $this->mongo->findById('fichier_metadata', $id);
        if (!$doc) {
            return $this->corsJson(['error' => 'Introuvable'], 404);
        }

        $path = $this->uploadService->getUploadDir() . '/' . $doc['filename'];
        if (!file_exists($path)) {
            return $this->corsJson(['error' => 'Fichier introuvable sur le disque'], 404);
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', $doc['mimeType']);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $doc['originalName']);
        return $response;
    }

    #[Route('/api/fichiers/{id}', name: 'api_fichiers_delete', methods: ['DELETE', 'OPTIONS'])]
    public function delete(string $id, Request $request): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->corsOptions('DELETE');
        }

        $doc = $this->mongo->findById('fichier_metadata', $id);
        if (!$doc) {
            return $this->corsJson(['error' => 'Fichier introuvable.'], 404);
        }

        $physicalPath = $this->uploadService->getUploadDir() . '/' . $doc['filename'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }

        $this->mongo->deleteById('fichier_metadata', $id);

        $logUserId    = (int) $request->headers->get('X-User-Id') ?: null;
        $logUserEmail = $request->headers->get('X-User-Email');
        $this->logService->log('DELETE_FILE', $logUserId, $logUserEmail, ['originalName' => $doc['originalName']]);

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
        $r->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $r->headers->set('Access-Control-Allow-Methods', $methods . ', OPTIONS');
        return $r;
    }
}
