<?php

namespace App\Service;

class LogService
{
    public function __construct(private MongoService $mongo) {}

    public function log(
        string $action,
        ?int $userId = null,
        ?string $userEmail = null,
        array $details = []
    ): void {
        $this->mongo->insertOne('action_log', [
            'action'    => $action,
            'userId'    => $userId,
            'userEmail' => $userEmail,
            'details'   => $details ?: null,
            'createdAt' => $this->mongo->now(),
        ]);
    }
}
