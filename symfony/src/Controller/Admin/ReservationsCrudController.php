<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Emprunt;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ReservationsCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field::new('id')->hideOnForm();
        yield DateTimeField::new('date_resa');
        yield AssociationField::new('adherent')->setLabel("Adhérent")->setFormTypeOption('constraints', [
            new Callback([$this, 'validateReservationCount']),
        ]);; 
        yield AssociationField::new('livre');
    }

    #[Route('/admin/reservations/{id}/transform-to-emprunt', name: 'admin_reservation_transform_to_emprunt')]
    public function transformToEmprunt(Reservation $reservation, Request $request): Response
    {
        $adherent = $reservation->getAdherent();
        $livre = $reservation->getLivre();  
        
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        $livre->removeReservation($reservation);
        $this->entityManager->persist($livre);
        $this->entityManager->flush();

        $emprunt = new Emprunt();
        $emprunt->setAdherent($adherent);
        $emprunt->setLivre($livre);
        $emprunt->setDateEmprunt(new \DateTime()); 
        $dateRetour = new \DateTime();
        $dateRetour->modify('+7 days');
        $emprunt->setDateRetour($dateRetour);

        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin', [
            'crudControllerFqcn' => EmpruntCrudController::class,
            'crudAction' => 'index', 
        ]);
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $action = Action::new('transform_to_emprunt', 'Transformer en emprunt'); 
        return $actions
            ->add(Crud::PAGE_INDEX, $action
                ->linkToRoute('admin_reservation_transform_to_emprunt', function (Reservation $reservation) {
                    return ['id' => $reservation->getId()];
                })
                ->setIcon('fa fa-exchange')
            );
    }

    public function validateReservationCount($value, ExecutionContextInterface $context): void
    {
        $reservation = $context->getRoot()->getData();
        $adherent = $reservation->getAdherent();
        
        if (count($adherent->getReservations()) >= 3) {
            $context->buildViolation('L\'adhérent a atteint le nombre maximal de réservations (3).')
                ->addViolation();
        }
    }
}
