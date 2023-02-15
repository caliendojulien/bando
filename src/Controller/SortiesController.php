<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
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
        Request $request
    ): Response {

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
