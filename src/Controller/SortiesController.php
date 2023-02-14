<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        SortieRepository $sortieRepository,
        Request $request
    ): Response
    {
        $campus = $request->query->get('campus');

        if ($campus) {
            $sorties = $sortieRepository->findByCampus($campus);
        } else {
            $sorties = $sortieRepository->findAll();
        }

        return $this->render('sorties/sorties.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/creer', name: '_creer-sortie')]
    public function creer(
        EntityManagerInterface $entityManager,
        CampusRepository $campusRepository,
        VilleRepository $villesRepo,
        Request $request
    ): Response {

        $user = $this->getUser();
        $sortie = new Sortie();
        //la sortie est à l'état "créée"
        $sortie->setEtat(1);
        $sortie->setOrganisateur($user);

        //mettre le campus de l'organisateur par défaut
        if (! $sortie->getCampus()) {
            $campus = $campusRepository->findOneBy(['nom' => $user->getUserIdentifier()]);
            $sortie->setCampus($campus);
        }
        $form = $this->createForm(SortieFormType::class, $sortie);
        // Todo trouver la date de fin en fonction de la durée

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('_sorties');
        }
        //passer la liste des villes avec les lieux par villes à la Form
        $villesEtLieux = $villesRepo->findAllAveclieux();
        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),"villesEtLieux"=>$villesEtLieux
        ]);
    }


}
