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
    public function index(EntityManagerInterface $em, Request $request, StagiaireRepository $stagiaireRepository, SluggerInterface $slugger): Response
    {
        $stagiaires = $this->getUser();
        $stagiaire = $stagiaireRepository->findOneBy(['email' => $stagiaires->getUserIdentifier()]);
        $profilForm = $this->createForm(ProfilType::class, $stagiaires);
        $profilForm->handleRequest($request);

        if ($profilForm->isSubmitted()) {
            $em->persist($stagiaires);
            $em->flush();
        }
        return $this->render('profil/modif.html.twig', [
            'profilForm' => $profilForm,
            'url' => $stagiaire->getImage()
        ]);
    }
}
