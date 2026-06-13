<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserApiController extends AbstractController
{

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);


        if (!$data || !isset($data['email'], $data['password'])) {
            $response = $this->json(['error' => 'Données incomplètes pour créer le compte.'], Response::HTTP_BAD_REQUEST);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }


        $user = new User();
        $user->setPrenom($data['prenom']);
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']); 

        // Hachage sécurisé du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Sauvegarde dans MySQL
        $em->persist($user);
        $em->flush();

        $response = $this->json([
            'success' => true,
            'message' => 'Compte TankRent configuré avec succès !'
        ], Response::HTTP_CREATED);
        
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    // 2. ROUTE FANTÔME DE CONNEXION (LOGIN)
    // C'est celle que notre "security.yaml" (json_login) va écouter en tâche de fond.
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // On laisse vide, Symfony l'intercepte tout seul !
        return $this->json(['message' => 'Connexion établie.']);
    }
}
