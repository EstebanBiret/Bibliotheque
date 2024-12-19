<?php

namespace App\Controller\Admin;

use App\Entity\ResponsableBib;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResponsableBibCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $encoder
    ) {}

    public static function getEntityFqcn(): string
    {
        return ResponsableBib::class;
    }
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
        ];
    }


    public function configureFields(string $pageName): iterable
    {
        $rolesField = ChoiceField::new('roles')
        ->setLabel('Rôles')
        ->setChoices([
            'Responsable bib' => 'ROLE_RESPONSABLE',
        ])
        ->allowMultipleChoices()
        ->renderExpanded()
        ->setFormTypeOptions([
            'data' => ['ROLE_RESPONSABLE'], 
        ]);

        return [
              $rolesField,
              EmailField::new('email'),
              TextField::new('password')
                  ->setFormType(RepeatedType::class)
                  ->setFormTypeOptions([
                      'type' => PasswordType::class,
                      'first_options' => ['label' => 'Password'],
                      'second_options' => ['label' => '(Repeat)'],
                      'mapped' => false,
                  ])
                  ->setRequired($pageName === Crud::PAGE_NEW)
                  ->onlyOnForms()
                  ,
        ];
    }
    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }
    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if ($password === null) {
                return;
            }

            $hash = $this->encoder->hashPassword($event->getData(), $password);
            $form->getData()->setPassword($hash);
        };
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
