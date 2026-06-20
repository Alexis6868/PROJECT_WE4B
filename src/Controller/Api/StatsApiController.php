<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Vehicule;
use App\Service\MongoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class StatsApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MongoService $mongo
    ) {}

    #[Route('/api/admin/stats', name: 'api_admin_stats', methods: ['GET', 'OPTIONS'])]
    public function stats(Request $request): JsonResponse
    {
        if ($request->getMethod() === 'OPTIONS') {
            $r = new JsonResponse(null, 204);
            $r->headers->set('Access-Control-Allow-Origin', '*');
            $r->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-User-Id, X-User-Email');
            $r->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
            return $r;
        }

        $conn = $this->em->getConnection();

        // ── KPIs MySQL ──────────────────────────────────────────────────────
        $totalVehicles     = $this->em->getRepository(Vehicule::class)->count([]);
        $totalReservations = $this->em->getRepository(Reservation::class)->count([]);
        $totalUsers        = $this->em->getRepository(User::class)->count([]);

        $avgPrix = (float) ($conn->executeQuery('SELECT COALESCE(AVG(prix),0) FROM reservation')->fetchOne());

        $revenueTotal = (float) ($conn->executeQuery('SELECT COALESCE(SUM(prix),0) FROM reservation')->fetchOne());

        // ── KPIs MongoDB ────────────────────────────────────────────────────
        $since7d = $this->mongo->toMongoDate(new \DateTimeImmutable('-7 days'));

        $loginsLast7 = $this->mongo->countDocuments('action_log', [
            'action'    => 'LOGIN',
            'createdAt' => ['$gte' => $since7d],
        ]);

        $fileStats = $this->mongo->aggregate('fichier_metadata', [
            ['$group' => ['_id' => null, 'count' => ['$sum' => 1], 'totalSize' => ['$sum' => '$size']]],
        ]);
        $totalFiles   = (int)   ($fileStats[0]['count']     ?? 0);
        $totalStorage = (int)   ($fileStats[0]['totalSize'] ?? 0);

        // ── Graphique : véhicules par type (MySQL) ───────────────────────────
        $vehiclesByType = $conn->executeQuery(
            'SELECT type, COUNT(*) as cnt FROM vehicule GROUP BY type ORDER BY cnt DESC'
        )->fetchAllAssociative();

        // ── Graphique : réservations par mois (MySQL, 6 derniers) ────────────
        $resByMonth = $conn->executeQuery(
            "SELECT DATE_FORMAT(date_debut,'%Y-%m') as label,
                    COUNT(*) as cnt,
                    COALESCE(SUM(prix),0) as revenue
             FROM reservation
             GROUP BY label
             ORDER BY label DESC
             LIMIT 6"
        )->fetchAllAssociative();
        // Remettre dans l'ordre chronologique
        $resByMonth = array_reverse($resByMonth);

        // ── Graphique : connexions par jour (MongoDB, 14 derniers jours) ──────
        $since14d = $this->mongo->toMongoDate(new \DateTimeImmutable('-14 days'));
        $loginsPerDay = $this->mongo->aggregate('action_log', [
            ['$match' => ['action' => 'LOGIN', 'createdAt' => ['$gte' => $since14d]]],
            ['$group' => [
                '_id'   => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$createdAt']],
                'count' => ['$sum' => 1],
            ]],
            ['$sort' => ['_id' => 1]],
        ]);

        // ── Graphique : uploads par jour (MongoDB, 14 derniers jours) ─────────
        $uploadsPerDay = $this->mongo->aggregate('fichier_metadata', [
            ['$match' => ['uploadedAt' => ['$gte' => $since14d]]],
            ['$group' => [
                '_id'   => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$uploadedAt']],
                'count' => ['$sum' => 1],
            ]],
            ['$sort' => ['_id' => 1]],
        ]);

        // ── Graphique : répartition des actions (MongoDB, all-time) ───────────
        $actionsBreakdown = $this->mongo->aggregate('action_log', [
            ['$group' => ['_id' => '$action', 'count' => ['$sum' => 1]]],
            ['$sort'  => ['count' => -1]],
        ]);

        // ── 300 derniers logs ─────────────────────────────────────────────────
        $logs = $this->mongo->find('action_log', [], [
            'sort'  => ['createdAt' => -1],
            'limit' => 300,
        ]);

        $response = $this->json([
            'kpi' => [
                'total_vehicles'     => $totalVehicles,
                'total_reservations' => $totalReservations,
                'total_users'        => $totalUsers,
                'logins_last_7_days' => $loginsLast7,
                'total_files'        => $totalFiles,
                'total_storage'      => $totalStorage,
                'avg_reservation'    => round($avgPrix, 2),
                'revenue_total'      => round($revenueTotal, 2),
            ],
            'charts' => [
                'vehiclesByType'   => $vehiclesByType,
                'reservationsByMonth' => $resByMonth,
                'loginsPerDay'     => $loginsPerDay,
                'uploadsPerDay'    => $uploadsPerDay,
                'actionsBreakdown' => $actionsBreakdown,
            ],
            'logs' => $logs,
        ]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
