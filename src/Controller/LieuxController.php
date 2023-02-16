<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    #[Route('/Creerlieu', name: 'creer_Lieu')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $lieu=new Lieu();
        $form=$this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        //traiter l'envoi du formulaire
        if ($form->isSubmitted() ) {
            if ($form->isValid()) {
                $entityManager->persist($lieu);
                $entityManager->flush();
                return $this->redirectToRoute('_creer-sortie');
            }
        }
            return $this->render('lieux/creer.html.twig', [ 'form' => $form->createView() ]);
    }
}
