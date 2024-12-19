<?php

namespace App\Controller\Admin;

use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Validator\Constraints\Callback;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class EmpruntCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Emprunt::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function validateEmpruntsCount($value, ExecutionContextInterface $context)
    {
        $emprunt = $context->getRoot()->getData();
        $adherent = $emprunt->getAdherent();
        $emprunts = $adherent->getEmprunts()->toArray();
        $empruntsNonRetournes = array_filter($emprunts, function ($emprunt) {
            return !$emprunt->isRetourne();
        });
        $empruntsCount = count($empruntsNonRetournes);

        if ($empruntsCount >= 5) {
            $context->buildViolation('Un adhérent ne peut pas avoir plus de 5 emprunts.')
                ->atPath('adherent')
                ->addViolation();
        }
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('dateEmprunt')->setLabel('Date d\'emprunt')->setRequired($pageName === Crud::PAGE_NEW)->setFormTypeOption('constraints', [
            new Callback([$this, 'validateDates']),
        ]);

        yield DateTimeField::new('dateRetour')->setLabel('Date de retour')->setRequired($pageName === Crud::PAGE_NEW)->setFormTypeOption('constraints', [
            new Callback([$this, 'validateDates']),
        ]);

        yield AssociationField::new('livre')->setLabel('Livre')->setRequired($pageName === Crud::PAGE_NEW)->setFormTypeOption('constraints', [
            new Callback([$this, 'validateDisponibiliteLivre']),
            new Callback([$this, 'setDisponibiliteLivre'])
        ])
        ;

        yield AssociationField::new('adherent')->setLabel('Adhérent')->setRequired($pageName === Crud::PAGE_NEW)->setFormTypeOption('constraints', [
            new Callback([$this, 'validateEmpruntsCount']),    
        ]);

        yield BooleanField::new('retourne')->setLabel('Livre retourné ?')->renderAsSwitch(false)->onlyOnIndex();
    }

    public function setDisponibiliteLivre($value, ExecutionContextInterface $context)
    {
        $emprunt = $context->getRoot()->getData();
        $livre = $emprunt->getLivre();
        $livre->setDisponible(false);
        $this->entityManager->persist($livre);
        $this->entityManager->flush();
    }

    public function validateDates($value, ExecutionContextInterface $context)
    {
        $emprunt = $context->getRoot()->getData();
        $dateEmprunt = $emprunt->getDateEmprunt();
        $dateRetour = $emprunt->getDateRetour();

        if ($dateEmprunt >= $dateRetour) {
            $context->buildViolation('La date de retour doit être ultérieure à la date d\'emprunt.')
                ->atPath('dateRetour')
                ->addViolation();
        }
    }

    public function validateDisponibiliteLivre($value, ExecutionContextInterface $context)
    {
        $emprunt = $context->getRoot()->getData();
        $livre = $emprunt->getLivre();

        if ($livre->getDisponible() === false) {
            $context->buildViolation('Le livre "' . $livre->getTitre() . '" n\'est pas disponible.')
                ->atPath('livre')
                ->addViolation();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        $action = Action::new('disponible_livre', 'Livre retourné'); 
        return $actions
            ->add(Crud::PAGE_INDEX, $action
                ->linkToRoute('admin_disponible_livre', function (Emprunt $emprunt) {
                    return ['id' => $emprunt->getId()];
                })
                ->setIcon('fa fa-share')
            );
    }

    #[Route('/admin/emprunts/{id}/disponible', name: 'admin_disponible_livre')]
    public function makeLivreRetourne(Emprunt $emprunt, Request $request):  Response
    {
        if($emprunt->isRetourne()){
            return $this->redirectToRoute('admin', [
                'crudControllerFqcn' => EmpruntCrudController::class,
                'crudAction' => 'index', 
            ]);
        }
        $livre = $emprunt->getLivre();
        $livre->setDisponible(true);
        $emprunt->setRetourne(true);

        $this->entityManager->persist($livre);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin', [
            'crudControllerFqcn' => EmpruntCrudController::class,
            'crudAction' => 'index', 
        ]);
    }
}
