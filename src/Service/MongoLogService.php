<?php

namespace App\Service;

class MongoLogService
{
    private $collection;
    private $fallbackMode = false;

    public function __construct()
{
        if (!class_exists('MongoDB\Client')) {
            $this->fallbackMode = true;
            return;
        }

        try {
            $uri = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
            $client = new \MongoDB\Client($uri);
            
        
            $this->collection = $client->tankrent_logs->activity_logs;
        } catch (\Exception $e) {
            $this->fallbackMode = true;
        }
    }

    //Enregistre une action dans la base de données MongoDB
public function logAction(string $action, ?int $userId, array $details = []): void
    {
        $document = [
            'action'    => $action,
            'userId'    => $userId,
            'details'   => $details,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM)
        ];


        if ($this->fallbackMode || !$this->collection) {
            $logDir = __DIR__ . '/../../var/log';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            // Sauvegarde sous forme de ligne JSON (conforme aux exigences de données semi-structurées)
            file_put_contents($logDir . '/mongodb_fallback.json', json_encode($document) . "\n", FILE_APPEND);
            return;
        }

        try {
            $this->collection->insertOne($document);
        } catch (\Exception $e) {
            // Deuxième sécurité en cas de coupure réseau avec le conteneur Mongo
            file_put_contents(__DIR__ . '/../../var/log/mongodb_fallback.json', json_encode($document) . "\n", FILE_APPEND);
        }
    }
}