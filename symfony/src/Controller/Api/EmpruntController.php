<?php

namespace App\Controller\Api;

use App\Repository\AdherentRepository;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpruntController extends AbstractController
{
    #[Route('/api/emprunts/adherent', methods: ['POST'])]
    public function getEmpruntsOfAdherent(Request $request, AdherentRepository $adheRepo,
                                          EmpruntRepository $empruntRepo): JsonResponse
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

        $emprunts = $empruntRepo->findByAdherent($adherent->getId());

        return $this->json($emprunts, context: ['groups' => ['emprunt:read', 'livre:id']]);
    }

}
