<?php

namespace App\Controller;

use App\Entity\Adherent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    private $passwordHasher;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/adherent', name: 'app-login', methods: ['POST'])]
    public function connexion(Request $request): Response
    {
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        // Assurez-vous que les données nécessaires sont présentes
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Il faut un email et un mot de passe.'], Response::HTTP_BAD_REQUEST);
        }

        // Recherchez l'adherent par e-mail
        $adherent = $this->entityManager->getRepository(Adherent::class)->findOneBy(['email' => $data['email']]);

        // Vérifiez si l'adherent existe et si le mot de passe est correct
        if (!$adherent || !$this->passwordHasher->isPasswordValid($adherent, $data['password'])) {
            return new JsonResponse(['error' => 'Login/mdp incorrects.'], Response::HTTP_UNAUTHORIZED);
        }

        // Connectez l'adherent (vous pouvez personnaliser cette partie selon votre logique)
        // $this->get('security.token_storage')->setToken(new UsernamePasswordToken($adherent, null, 'main', $adherent->getRoles()));

        return $this->json([
            'user' => $adherent->getId(),
            'message' => 'Login successful.'
        ]);
    }
}
