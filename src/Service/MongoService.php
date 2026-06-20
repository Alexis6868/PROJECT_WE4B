<?php

namespace App\Service;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

class MongoService
{
    private Manager $manager;

    public function __construct(
        private string $mongoUrl,
        private string $mongoDb
    ) {
        $this->manager = new Manager($mongoUrl);
    }

    /**
     * Insère un document dans une collection et retourne son ID (string hex).
     */
    public function insertOne(string $collection, array $doc): string
    {
        $id = new ObjectId();
        $doc['_id'] = $id;

        $bulk = new BulkWrite();
        $bulk->insert($doc);
        $this->manager->executeBulkWrite("{$this->mongoDb}.{$collection}", $bulk);

        return (string) $id;
    }

    /**
     * Récupère des documents. Retourne un tableau PHP pur (sans types BSON).
     * Options: sort, limit, skip (ex: ['sort' => ['createdAt' => -1], 'limit' => 100])
     */
    public function find(string $collection, array $filter = [], array $options = []): array
    {
        $query  = new Query($filter, $options);
        $cursor = $this->manager->executeQuery("{$this->mongoDb}.{$collection}", $query);
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $results = [];
        foreach ($cursor as $doc) {
            $results[] = $this->normalize($doc);
        }
        return $results;
    }

    /**
     * Récupère un seul document ou null.
     */
    public function findOne(string $collection, array $filter): ?array
    {
        $rows = $this->find($collection, $filter, ['limit' => 1]);
        return $rows[0] ?? null;
    }

    /**
     * Récupère un document par son _id (string hex).
     */
    public function findById(string $collection, string $id): ?array
    {
        return $this->findOne($collection, ['_id' => new ObjectId($id)]);
    }

    /**
     * Supprime un document par critère.
     */
    public function deleteOne(string $collection, array $filter): void
    {
        $bulk = new BulkWrite();
        $bulk->delete($filter, ['limit' => 1]);
        $this->manager->executeBulkWrite("{$this->mongoDb}.{$collection}", $bulk);
    }

    /**
     * Supprime un document par son _id (string hex).
     */
    public function deleteById(string $collection, string $id): void
    {
        $this->deleteOne($collection, ['_id' => new ObjectId($id)]);
    }

    /**
     * Exécute un pipeline d'agrégation et retourne les résultats normalisés.
     * Pipeline est un tableau de stages MongoDB (ex: [['$group' => ...], ['$sort' => ...]])
     */
    public function aggregate(string $collection, array $pipeline): array
    {
        $cmd = new Command([
            'aggregate' => $collection,
            'pipeline'  => $pipeline,
            'cursor'    => (object) [],
        ]);
        $cursor = $this->manager->executeCommand($this->mongoDb, $cmd);
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

        $results = [];
        foreach ($cursor as $doc) {
            $results[] = $this->normalize($doc);
        }
        return $results;
    }

    /**
     * Compte les documents matchant un filtre.
     */
    public function countDocuments(string $collection, array $filter = []): int
    {
        $cmd    = new Command(['count' => $collection, 'query' => (object) $filter]);
        $cursor = $this->manager->executeCommand($this->mongoDb, $cmd);
        $result = current($cursor->toArray());
        return (int) ($result->n ?? 0);
    }

    /**
     * Retourne un UTCDateTime MongoDB pour maintenant.
     */
    public function now(): UTCDateTime
    {
        return new UTCDateTime((int) (microtime(true) * 1000));
    }

    /**
     * Retourne un UTCDateTime MongoDB pour une DateTimeImmutable.
     */
    public function toMongoDate(\DateTimeImmutable $dt): UTCDateTime
    {
        return new UTCDateTime($dt->getTimestamp() * 1000);
    }

    /**
     * Convertit un document BSON brut en tableau PHP sérialisable en JSON.
     * _id (ObjectId) → 'id' (string), UTCDateTime → string formatée.
     */
    private function normalize(array $doc): array
    {
        $out = [];
        foreach ($doc as $key => $value) {
            $normalized = $key === '_id' ? 'id' : $key;
            if ($value instanceof ObjectId) {
                $out[$normalized] = (string) $value;
            } elseif ($value instanceof UTCDateTime) {
                $out[$normalized] = $value->toDateTime()
                    ->setTimezone(new \DateTimeZone('Europe/Paris'))
                    ->format('Y-m-d H:i:s');
            } elseif (is_array($value)) {
                $out[$normalized] = $this->normalizeArray($value);
            } else {
                $out[$normalized] = $value;
            }
        }
        return $out;
    }

    private function normalizeArray(array $arr): array
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if ($v instanceof ObjectId) {
                $out[$k] = (string) $v;
            } elseif ($v instanceof UTCDateTime) {
                $out[$k] = $v->toDateTime()->format('Y-m-d H:i:s');
            } elseif (is_array($v)) {
                $out[$k] = $this->normalizeArray($v);
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
