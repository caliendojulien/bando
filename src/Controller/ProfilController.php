<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\ProfilType;
use App\Repository\StagiaireRepository;
use App\Services\EtatSorties;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\En;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_modif')]
    public function index(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $stagiaire = $this->getUser();
        $profilForm = $this->createForm(ProfilType::class, $stagiaire);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()){
            if(!preg_match('/^(?=.{8,}$)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?\W).*$/',$profilForm['password']->getData())){
                $this->addFlash('erreur','Le mot de passe ne respect pas le format requis (Au minimum 8 caractères, une minuscule, une majuscule et 1 signe spécial');
            }else if (
                empty($profilForm->get('nom')->getData()) ||
                strlen($profilForm->get('nom')->getData()) > 255
            ) {
                $this->addFlash('erreur', 'Mauvais format du nom');
            } else if (
                empty($profilForm->get('prenom')->getData()) ||
                strlen($profilForm->get('prenom')->getData()) > 255
            ) {
                $this->addFlash('erreur', 'Mauvais format du prenom');
            } else if (
                empty($profilForm->get('telephone')->getData()) ||
                strlen($profilForm->get('telephone')->getData()) != 10
            ) {
                $this->addFlash('erreur', 'Mauvais format du numéro de téléphone');
            } else {
                $stagiaire = $profilForm->getData();
                $encoded_password = $passwordHasher->hashPassword($stagiaire, $profilForm['password']->getData());
                $stagiaire->setPassword($encoded_password);
                $em->persist($stagiaire);
                $em->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour !');
            }
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
