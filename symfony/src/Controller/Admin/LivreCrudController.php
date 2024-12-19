<?php

namespace App\Controller\Admin;

use App\Entity\Livre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class LivreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Livre::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')->hideOnForm(),
            TextField::new('titre')->setRequired($pageName === Crud::PAGE_NEW),
            DateField::new('date_sortie')->setRequired($pageName === Crud::PAGE_NEW),
            TextField::new('langue')->setRequired($pageName === Crud::PAGE_NEW),
            ImageField::new('photo_couverture')->hideOnForm(),
            UrlField::new('photo_couverture')->setLabel('Photo de couverture (lien)')->setRequired($pageName === Crud::PAGE_NEW)->hideOnIndex(),
            BooleanField::new('disponible')->renderAsSwitch(false),
            AssociationField::new('auteurs')->setRequired($pageName === Crud::PAGE_NEW),
            AssociationField::new('categories')->hideOnForm(),
            AssociationField::new('emprunts')->hideOnForm(),
            AssociationField::new('reservation')->hideOnForm(),
        ];
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
}
