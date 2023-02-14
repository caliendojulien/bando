<?php

namespace App\Controller\Admin;

use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StagiaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stagiaire::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('password')->hideOnForm(),
            TextField::new('email'),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('telephone'),
            ArrayField::new('roles')->hideOnForm(),
            BooleanField::new('administrateur')->setHelp("n'activer que si nécessaire"),
            BooleanField::new('actif')->setValue(true),
            BooleanField::new('premiereConnexion')->hideOnForm(),
            AssociationField::new('campus'),
        ];
    }

    /**
     * Surcharge de la méthode de la classe AbstractCrudController appelée lors de la création d'une entité
     * @param EntityManagerInterface $em
     * @param $stag
     * @return void
     */
    public function persistEntity(EntityManagerInterface $em, $stag): void
    {
        if (!$stag instanceof Stagiaire) return;
        //Todo : chiffrer le mot de passe avant son envoi
       // $stag->setPassword("Passw0rd");
        $stag->setPassword("$2y$13\$zwa64OHleb.MuWUuCBi2yeT6ZHM28wyiSNacOIKhflcdO3OyMYwui");
        //par défaut le stagiaire est actif
        $stag->setPremiereConnexion(true);
        //gestion des rôles : USER par défaut, Admin en plus si la case est cochée
        $roles=["ROLE_USER"];
        if ($stag->isAdministrateur())$roles[]="ROLE_ADMIN";
        $stag->setRoles($roles);
        parent::persistEntity($em, $stag);
    }

    /**
     * Surcharge de la méthode de la classe AbstractCrudController appelée lors de la modification d'une entité
     *
     * @param EntityManagerInterface $entityManager
     * @param $stag
     * @return void
     */
    public function updateEntity(EntityManagerInterface $entityManager, $stag): void
    {  if (!$stag instanceof Stagiaire) return;
        //mise à jour des rôles lors de l'UPDATE
        $stag->setRoles([]); $roles=["ROLE_USER"];
        if ($stag->isAdministrateur())$roles[]="ROLE_ADMIN";
        $stag->setRoles($roles);
        parent::persistEntity($entityManager, $stag);
    }
}
