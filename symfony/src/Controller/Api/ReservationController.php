<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Repository\AdherentRepository;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/api/reservations', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ReservationRepository $resaRepo,
                           AdherentRepository $adheRepo, LivreRepository $livreRepo,
                           EmpruntRepository $empruntRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // check arguments
        if (empty($data['sess_id']) || empty($data['livre_id']))
            return $this->json(['erreur' => 'Vous devez fournir un sess_id et un livre_id.',
                'code' => 1], Response::HTTP_BAD_REQUEST);

        // check session
        session_id($data['sess_id']);
        session_start();
        if (empty($_SESSION['id_adherent']))
            return $this->json(['erreur' => 'Votre session n\'est pas valide.',
                'code' => 2], Response::HTTP_UNAUTHORIZED);

        $adherent = $adheRepo->find($_SESSION['id_adherent']);

        // check réservations < 3
        $reservations = $resaRepo->findByAdherent($adherent->getId());
        if (count($reservations) > 2)
            return $this->json(['erreur' => 'Cet adhérent a déjà 3 réservations, il ne peut pas en avoir plus.',
                'code' => 3], Response::HTTP_BAD_REQUEST);

        // check livre
        $livre = $livreRepo->find($data['livre_id']);
        if (!$livre)
            return $this->json(['erreur' => 'Cet ID ne correspond à aucun livre.'], Response::HTTP_BAD_REQUEST);

        // check livre pas déjà réservé
        $reservations = $resaRepo->findByLivre($livre->getId());
        if (!empty($reservations))
            return $this->json(['erreur' => 'Ce livre a déjà été réservé.', 'code' => 4], Response::HTTP_BAD_REQUEST);

        // check livré pas déjà emprunté
        $date_resa = new \DateTimeImmutable('today');
        $emprunts = $empruntRepo->findValidByLivre($livre->getId(), $date_resa);
        if (!empty($emprunts))
            return $this->json(['erreur' => 'Ce livre est déjà emprunté.', 'code' => 5], Response::HTTP_BAD_REQUEST);

        $reservation = new Reservation();
        $reservation->setAdherent($adherent);
        $reservation->setLivre($livre);
        $reservation->setDateResa($date_resa);
        $livre->setDisponible(false);

        $entityManager->persist($reservation);
        $entityManager->persist($livre);
        $entityManager->flush();

        return $this->json($reservation, Response::HTTP_CREATED, [],
            ['groups' => 'reservation:read', 'adherent:read', 'livre:read']);
    }

    #[Route('/api/reservations/adherent', methods: ['POST'])]
    public function getReservationsOfAdherent(Request $request, ReservationRepository $resaRepo,
                                              AdherentRepository $adheRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // check argument
        if (empty($data['sess_id']))
            return $this->json(['erreur' => 'Vous devez fournir un sess_id pour prouver votre authenticité.',
                'code' => 1], Response::HTTP_BAD_REQUEST);

        // check session
        session_id($data['sess_id']);
        session_start();
        if (empty($_SESSION['id_adherent']))
            return $this->json(['erreur' => 'Votre session n\'est pas valide.',
                'code' => 2], Response::HTTP_UNAUTHORIZED);

        $adherent = $adheRepo->find($_SESSION['id_adherent']);

        $reservations = $resaRepo->findByAdherent($adherent->getId());

        return $this->json($reservations, context: ['groups' => ['reservation:read', 'livre:id']]);
    }

    #[Route('/api/reservation', methods: ['POST'])]
    public function remove(Request $request, EntityManagerInterface $entityManager,
                           ReservationRepository $resaRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // check arg
        if (empty($data['sess_id']) || empty($data['resa_id']))
            return $this->json(['erreur' => 'Vous devez fournir un sess_id et un resa_id.',
                'code' => 1], Response::HTTP_BAD_REQUEST);

        // check session
        session_id($data['sess_id']);
        session_start();
        if (empty($_SESSION['id_adherent']))
            return $this->json(['erreur' => 'Votre session n\'est pas valide.',
                'code' => 2], Response::HTTP_UNAUTHORIZED);

        $reservation = $resaRepo->find($data['resa_id']);

        if ($reservation->getAdherent()->getId() == $_SESSION['id_adherent']) {
            $livre = $reservation->getLivre();
            $livre->setDisponible(true);
            $entityManager->persist($livre);
            $reservation->setLivre(null);
            $reservation->setAdherent(null);
            $entityManager->remove($reservation);
            $entityManager->flush();
            return $this->json('');
        }

        return $this->json(['erreur' => 'Cette réservation n\'existe pas',
            'code' => 3], Response::HTTP_BAD_REQUEST);
    }
}
