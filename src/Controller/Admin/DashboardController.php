<?php

namespace App\Controller\Admin;

use App\Controller\ImportStagiairesController;
use App\Entity\Campus;
use App\Entity\Lieu;
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
     //   return parent::index();

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
         return $this->render('Admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration de Bando');
    }

    public function configureMenuItems(): iterable
    {
        //Ajout des CRUD et du lien de retour au site
        yield MenuItem::linkToCrud('Campus', 'fas fa-list', Campus::class);
        yield MenuItem::linkToCrud('Villes', 'fas fa-map-marker-alt', Ville::class);
        yield MenuItem::linkToCrud('Lieux', 'fas fa-map-marker-alt', Lieu::class);
        yield MenuItem::linkToCrud('Stagiaires', 'fas fa-list', Stagiaire::class);
        //Todo choisi une autre route quand elle sera créée
        yield MenuItem::linktoRoute('Retour aux sorties', 'fas fa-home', 'sorties_liste');
        yield MenuItem::linktoRoute('Importer des stagiaires (xls/csv)', 'fas fa-file-upload', '_import-stagiaires');
    }
}
