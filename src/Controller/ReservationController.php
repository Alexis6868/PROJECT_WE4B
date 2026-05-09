<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Vehicule;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $id_user = $this->getUser();

        // Si le rôle admin est affecté cela affiche toutes les réservation
        if ($this->isGranted('ROLE_ADMIN')) {
            $reservations = $reservationRepository->findAll();
        } 
        //Sinon uniquement celle de l'utilisateur connecté
        else {
            $reservations = $reservationRepository->findBy(['id_user' => $id_user]);
        }

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }




    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();

        //Recuperation de l'ID du parametre de l'URL
        $id_vehicule = $request->query->get('id_vehicule');
        if ($id_vehicule) {
            $vehicule = $entityManager->getRepository(Vehicule::class)->find($id_vehicule);
            $reservation->setIdVehicule($vehicule);
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $debut = $reservation->getDateDebut();
        $fin = $reservation->getDateFin();

            if (($fin < $debut) || ($debut < new \DateTime())) {
                $this->addFlash('error', 'La date de fin doit être supérieure à la date de début.');
                return $this->render('reservation/new.html.twig', [
                    'reservation' => $reservation,
                    'form' => $form,
                ]);
            }


            
            $interval = $debut->diff($fin);
            $jours = $interval->days + 1;
            $prix = '1000' * $jours;


            $reservation->setPrix($prix);
            $reservation->setIdUser($this->getUser());
            $reservation->getIdVehicule()?->setEtat('Réservé');

            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a bien été enregistrée.');

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
