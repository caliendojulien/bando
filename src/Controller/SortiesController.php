<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Form\SortieSearchFormType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{

    #[Route('/', name: 'sorties_test')]
    public function test(): Response
    {
        return $this->render('sorties/test.html.twig', []);
    }

    #[Route('/sorties', name: '_sorties')]
    public function sorties(
        SortieRepository     $sortieRepository,
        Request              $request,
        FormFactoryInterface $formFactory
    ): Response
    {
        // Création du formulaire de recherche de sorties
        $form = $formFactory->create(SortieSearchFormType::class);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = [
                'nom' => $form->get('nom')->getData(),
                'debutSortie' => $form->get('debutSortie')->getData(),
                'finSortie' => $form->get('finSortie')->getData(),
                'campus' => $form->get('campus')->getData(),
                'organisateur' => $form->get('organisateur')->getData(),
                'inscrit' => $form->get('inscrit')->getData(),
                'non_inscrit' => $form->get('non_inscrit')->getData(),
                'sorties_passees' => $form->get('sorties_passees')->getData()
            ];

            // Si la case "Sorties passées" est cochée, on ignore la date de début de la sortie
            if ($data['sorties_passees']) {
                $data['debutSortie'] = null;
            }

            // Recherche des sorties en fonction des données renseignées par l'utilisateur
            $sorties = $sortieRepository->findSorties(
                $data['nom'],
                $data['debutSortie'],
                $data['finSortie'],
                $data['campus'],
                $data['organisateur'],
                $this->getUser(),
                $data['inscrit'],
                $data['non_inscrit'],
                $data['sorties_passees']
            );
        } else {
            // Si le formulaire n'a pas été soumis ou n'est pas valide, récupération de toutes les sorties
            $sorties = $sortieRepository->findAll();
        }

        // Rendu de la vue et envoi des données
        return $this->render('sorties/sorties.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/creer', name: '_creer-sortie')]
    public function creer(
        EntityManagerInterface $entityManager,
        CampusRepository       $campusRepository,
        Request                $request
    ): Response
    {

        $user = $this->getUser();
        $sortie = new Sortie();
        $sortie->setEtat(1);
        $sortie->setOrganisateur($user);
        $campus = $campusRepository->findOneBy(['nom' => $user->getUserIdentifier()]);
        $sortie->setCampus($campus);
        $form = $this->createForm(SortieFormType::class, $sortie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('_sorties');
        }

        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
