<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Stagiaire;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'profil_modif')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $stagiaires = new Stagiaire();
        $stagiaires = $this->getUser();
        $campus = $em->getRepository(Campus::class)->findAll();
        $profilForm = $this->createForm(ProfilType::class, $stagiaires);

        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {
            $em->persist($stagiaires);
            $em->flush();
        }

        return $this->render('profil/modif.html.twig', [
            'profilForm' => $profilForm,
        ]);
    }
}
