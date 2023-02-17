<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\StagiaireRepository;
use App\Services\EtatSorties;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Validator\Constraints\Regex;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_modif')]
    public function index(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $stagiaireConnecte = $this->getUser();
        if (is_null($stagiaireConnecte)) {
            return $this->redirectToRoute('app_login');
        }
        $stagiaire = $stagiaireRepository->findOneBy(['email' => $stagiaireConnecte->getUserIdentifier()]);
        $profilForm = $this->createForm(ProfilType::class, $stagiaireConnecte);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $plaintextPassword = $profilForm->get('password')->getData();
            if (!$passwordHasher->isPasswordValid($stagiaire, $plaintextPassword)) {
                $this->addFlash('erreur', 'Mauvais format mot de passe ');
            } else if (
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
            } else if (
                (strlen($profilForm->get('password')->getData()) < 8 && !empty($profilForm->get('password')->getData())) ||
                preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $profilForm->get('telephone')->getData())
            ) {
                $this->addFlash('erreur', 'Mauvais format mot de passe');
            } else {
                $em->persist($stagiaireConnecte);
                $em->flush();
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

    #[Route('/testService', name: 'test-service')]
    public function service(EtatSorties $etatSorties): Response
    {
        $etatSorties->updateEtatSorties();
        return $this->render('sorties/test.html.twig', [
        ]);
    }


}
