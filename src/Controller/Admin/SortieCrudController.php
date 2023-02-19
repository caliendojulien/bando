<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Stagiaire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->onlyOnIndex(),
            AssociationField::new('lieu')
                ->formatValue(function ($value, $entity) {
                    return $entity->getLieu()->getNom();
                })
                ->setFormType(EntityType::class)
                ->setFormTypeOptions([
                    'class' => Lieu::class,
                    'choice_label' => 'nom',
                ])->onlyOnIndex(),
            AssociationField::new('campus')
                ->formatValue(function ($value, $entity) {
                    return $entity->getCampus()->getNom();
                })
                ->setFormType(EntityType::class)
                ->setFormTypeOptions([
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                ])->onlyOnIndex(),
            AssociationField::new('organisateur')
                ->formatValue(function ($value, $entity) {
                    return $entity->getOrganisateur()->getNom();
                })
                ->setFormType(EntityType::class)
                ->setFormTypeOptions([
                    'class' => Stagiaire::class,
                    'choice_label' => 'nom',
                ])->onlyOnIndex(),
            TextField::new('nom')->onlyOnIndex(),
            DateTimeField::new('debutSortie')->onlyOnIndex(),
            DateTimeField::new('finSortie')->onlyOnIndex(),
            DateTimeField::new('dateLimiteInscription')->onlyOnIndex(),
            TextField::new('infosSortie')->onlyOnIndex(),
            IntegerField::new('etat'),
            TextField::new('motifAnnulation'),
        ];

    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
            ;
    }


}
