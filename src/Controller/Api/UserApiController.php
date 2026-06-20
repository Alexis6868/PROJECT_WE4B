<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserApiController extends AbstractController
{
    public function __construct(private LogService $logService) {}
    #[Route('/api/register', name: 'api_register', methods: ['POST', 'OPTIONS'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $em
    ): JsonResponse {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            return $response;
        }

        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            $response = $this->json(['error' => 'Données incomplètes pour créer le compte.'], Response::HTTP_BAD_REQUEST);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        $user = new User();
        $user->setPrenom($data['prenom'] ?? '');
        $user->setNom($data['nom'] ?? '');
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']); 

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        $response = $this->json([
            'success' => true,
            'message' => 'Compte TankRent configuré avec succès !'
        ], Response::HTTP_CREATED);
        
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST', 'OPTIONS'])]
    public function login(Request $request): JsonResponse
    {

        if ($request->getMethod() === 'OPTIONS') {
            $response = new JsonResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            return $response;
        }

        $user = $this->getUser();

        if (!$user) {
            $response = $this->json(['error' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        if ($user instanceof User) {
            $this->logService->log('LOGIN', $user->getId(), $user->getUserIdentifier());
        }

        // 🚀 On renvoie enfin les clés attendues par ton AuthService Angular !
        $response = $this->json([
            'message' => 'Connexion établie.',
            'id'      => $user->getId(),
            'nom'     => $user->getNom(),
            'email'   => $user->getUserIdentifier(),
            'isAdmin' => in_array('ROLE_ADMIN', $user->getRoles()),
        ]);
        
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}