<?php

namespace App\Controller\Admin;

use App\Entity\Adherent;
use App\Entity\Auteur;
use App\Entity\Bibliothecaire;
use App\Entity\Categorie;
use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Entity\Reservation;
use App\Entity\ResponsableBib;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $emprunts = $this->entityManager->getRepository(Emprunt::class)->findBy([], ['date_retour' => 'ASC']);
        $totalEmprunts = count($emprunts);

        $finishedCount = 0;
        $lateCount = 0;

        foreach ($emprunts as $emprunt) {
            if ($emprunt->isRetourne()) {
                $finishedCount++;
            } elseif ($emprunt->getDateRetour() < new \DateTime()) {
                $lateCount++;
            }
        }

        $finishedPercentage = $totalEmprunts > 0 ? ($finishedCount / $totalEmprunts) * 100 : 0;
        $latePercentage = $totalEmprunts > 0 ? ($lateCount / $totalEmprunts) * 100 : 0;

        return $this->render('admin/dashboard.html.twig', [
            'emprunts' => $emprunts,
            'finishedCount' => $finishedCount,
            'finishedPercentage' => $finishedPercentage,
            'lateCount' => $lateCount,
            'latePercentage' => $latePercentage,
        ]);
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SAE S6 Bibliotheque')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Utilisateurs');
        if($this->isGranted('ROLE_RESPONSABLE')){
            yield MenuItem::linkToCrud('Adhérent', 'fa fa-user', Adherent::class);
        }
        yield MenuItem::linkToCrud('Réservation', 'fa fa-key', Reservation::class);
        yield MenuItem::linkToCrud('Emprunt', 'fa fa-handshake-o', Emprunt::class);
        if($this->isGranted('ROLE_RESPONSABLE')){
            yield MenuItem::section('Bibliothèque');
            yield MenuItem::linkToCrud('Livre', 'fa fa-book', Livre::class);
            yield MenuItem::linkToCrud('Auteur', 'fa fa-user', Auteur::class);
            yield MenuItem::linkToCrud('Categorie', 'fa fa-folder', Categorie::class);
            yield MenuItem::section('Employés');
            yield MenuItem::linkToCrud('Bibliothécaire', 'fa fa-user', Bibliothecaire::class);
            yield MenuItem::linkToCrud('Responsable Bibliothèque', 'fa fa-user', ResponsableBib::class);   
        }
    }
}
