<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\ProfilType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_modif')]
    public function index(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaireConnecte = $this->getUser();
        $stagiaire = $stagiaireRepository->findOneBy(['email' => $stagiaireConnecte->getUserIdentifier()]);
        $profilForm = $this->createForm(ProfilType::class, $stagiaireConnecte);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted()) {
            $em->persist($stagiaireConnecte);
            $em->flush();
        }
        return $this->render('profil/modif.html.twig', [
            'profilForm' => $profilForm,
            'stagiaire' => $stagiaire
        ]);
    }

    #[Route('/profilAffiche/{id}', name: 'profil_affich')]
    public function affiche(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository, int $id): Response
    {
        //Récupération du stagiaire passé en paramètre de l'appel au contrôleur.
        $stagiaire = $stagiaireRepository->findOneBy(['id' => $id]);

        //Récupération du stagiaire connecté
        $stagiaireConnecteInterface = $this->getUser();
        $stagiaireConnecte = $stagiaireRepository->findOneBy(['email' => $stagiaireConnecteInterface->getUserIdentifier()]);

        //Si le stagiaire connecté est le stagiaire recherché alors il est renvoyé vers la page de modification du profil
        if ($stagiaire->getId() == $stagiaireConnecte->getId()) {
            return $this->redirectToRoute('profil_modif');
        }
        return $this->render('profil/affiche.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
