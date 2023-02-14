<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    #[Route('/profil', name: 'profil_modif')]
    public function index(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaires = $this->getUser();
        $profilForm = $this->createForm(ProfilType::class, $stagiaires);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted()) {
            dump("test");
            $em->persist($stagiaires);
            $em->flush();

        }

        return $this->render('profil/modif.html.twig', [
            'profilForm' => $profilForm,
            'url' => $profilForm->get('url_photo')->getData()
        ]);
    }
}
