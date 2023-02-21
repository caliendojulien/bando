<?php

namespace App\Controller\Admin;

use App\Controller\ImportStagiairesController;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Stagiaire;
use App\Entity\Ville;
use App\Form\ImportStagiairesFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('Admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration de Bando');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Campus', 'fas fa-school', Campus::class);
        yield MenuItem::linkToCrud('Sorties', 'fas fa-champagne-glasses', Sortie::class);
        yield MenuItem::subMenu('Localisations', 'fas fa-map-marker-alt')->setSubItems([
            MenuItem::linkToCrud('Villes', 'fas fa-map-marker-alt', Ville::class)->setCssClass('text-primary'),
            MenuItem::linkToCrud('Lieux', 'fas fa-map-marker-alt', Lieu::class)->setCssClass('text-primary'),
        ]);
        yield MenuItem::linkToCrud('Stagiaires', 'fas fa-graduation-cap', Stagiaire::class);
        yield MenuItem::linktoRoute('Importer des stagiaires (xls/csv)', 'fas fa-file-upload', '_import-stagiaires');
        yield MenuItem::linktoRoute('Retour aux sorties', 'fas fa-home', 'sorties_liste');
    }
}
