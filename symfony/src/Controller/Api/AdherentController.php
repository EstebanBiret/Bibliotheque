<?php

namespace App\Controller\Api;

use App\Repository\AdherentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdherentController extends AbstractController
{
    #[Route('/api/adherent', methods: ['POST'])]
    public function login(Request $request, AdherentRepository $adheRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // check arguments
        if (empty($data['email']) || empty($data['password']))
            return $this->json(['erreur' => 'Il faut un email et un mot de passe.',
                'code' => 1], Response::HTTP_BAD_REQUEST);

        // check adherent
        $adherent = $adheRepo->findOneByEmail($data['email']);
        if (!$adherent)
            return $this->json(['erreur' => 'Compte inexistant.', 'code' => 2],
                Response::HTTP_BAD_REQUEST);

        // check password
        if (!password_verify($data['password'], $adherent->getPassword()))
            return $this->json(['erreur' => 'Mot de passe incorrect.',
                'code' => 3], Response::HTTP_BAD_REQUEST);

        session_start();
        $_SESSION['id_adherent'] = $adherent->getId();
        $adherent->setSessId(session_id());

        return $this->json($adherent, context: ['groups' => 'adherent:read']);
    }

    #[Route('/api/adherent/edit', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager,
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

        $allowedArgs = ['nom', 'prenom', 'email', 'new_password', 'old_password', 'adresse', 'telephone', 'photo'];

        $validArgs = [];
        foreach (array_keys($data) as $arg) {
            if (!empty($data[$arg]) && in_array($arg, $allowedArgs))
                $validArgs[] = $arg;
        }

        // check arguments
        if (empty($validArgs))
            return $this->json(['erreur' => 'Mauvaise utilisation de l\'API, il faut un ou plus de nom, 
                prenom, email, new_password, old_password, adresse, telephone, photo.',
                'code' => 3], Response::HTTP_BAD_REQUEST);

        // edit adhérent
        foreach ($validArgs as $arg) {
            switch ($arg) {
                case 'nom':
                    $adherent->setNom($data[$arg]);
                    break;
                case 'prenom':
                    $adherent->setPrenom($data[$arg]);
                    break;
                case 'email':
                    $adherent->setEmail($data[$arg]);
                    break;
                case 'new_password':
                    if (empty($data['old_password']))
                        return $this->json(['erreur' => 'Vous devez fournir votre mot de passe actuel pour pouvoir le changer.', 'code' => 4], Response::HTTP_BAD_REQUEST);
                    if ($data['old_password'] == $data[$arg])
                        return $this->json(['erreur' => 'Le nouveau mot de passe doit être différent de l\'ancien.', 'code' => 5], Response::HTTP_BAD_REQUEST);
                    if (!password_verify($data['old_password'], $adherent->getPassword()))
                        return $this->json(['erreur' => 'Le mot de passe actuel est incorrect.', 'code' => 6], Response::HTTP_BAD_REQUEST);
                    $adherent->setPassword(password_hash($data[$arg], PASSWORD_DEFAULT));
                    break;
                case 'adresse':
                    $adherent->setAdresse($data[$arg]);
                    break;
                case 'telephone':
                    $adherent->setTelephone($data[$arg]);
                    break;
                case 'photo':
                    $adherent->setPhoto($data[$arg]);
                    break;
            }
        }

        $entityManager->persist($adherent);
        $entityManager->flush();

        return $this->json($adherent, context: ['groups' => 'adherent:read']);
    }

    #[Route('/api/adherent/get', methods: ['POST'])]
    public function get(Request $request, AdherentRepository $adheRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // check arguments
        if (empty($data['sess_id']))
            return $this->json(['erreur' => 'Vous devez fournir un sess_id.',
                'code' => 1], Response::HTTP_BAD_REQUEST);

        // check session
        session_id($data['sess_id']);
        session_start();
        if (empty($_SESSION['id_adherent']))
            return $this->json(['erreur' => 'Votre session n\'est pas valide.',
                'code' => 2], Response::HTTP_UNAUTHORIZED);

        $adherent = $adheRepo->find($_SESSION['id_adherent']);

        return $this->json($adherent, context: ['groups' => 'adherent:read']);
    }

}
