<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class UploadService
{
    private string $uploadDir;

    public function __construct(
        private MongoService $mongo,
        KernelInterface $kernel
    ) {
        $this->uploadDir = $kernel->getProjectDir() . '/public/uploads';
    }

    /**
     * Déplace le fichier sur disque et persiste les métadonnées dans MongoDB.
     * Retourne le document inséré (avec son 'id' MongoDB).
     */
    public function upload(UploadedFile $file, ?int $userId = null): array
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $size         = (int) $file->getSize();
        $originalName = $file->getClientOriginalName();
        $mimeType     = $file->getMimeType() ?? 'application/octet-stream';
        $filename     = uniqid('', true) . '.' . $file->guessExtension();

        $file->move($this->uploadDir, $filename);

        $doc = [
            'filename'     => $filename,
            'originalName' => $originalName,
            'mimeType'     => $mimeType,
            'size'         => $size,
            'uploadedAt'   => $this->mongo->now(),
            'userId'       => $userId,
        ];

        $id = $this->mongo->insertOne('fichier_metadata', $doc);
        $doc['id'] = $id;

        return $doc;
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}
