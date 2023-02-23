<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class LieuxController extends AbstractController
{
    /**
     * Création d'un lieu (par n'importe quel stagiaire)
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/lieu/Creerlieu', name: 'creer_lieu')]
    public function index(
        Request                $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        try {
            $lieu = new Lieu();
            $form = $this->createForm(LieuType::class, $lieu);
            $form->handleRequest($request);

            //traiter l'envoi du formulaire
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $entityManager->persist($lieu);
                    $entityManager->flush();
                    return $this->redirectToRoute('sorties_creer');
                }
            }
            return $this->render('lieux/creer.html.twig', ['form' => $form->createView()]);
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

//
    #[isGranted("ROLE_USER")]
    #[Route('/lieu/listerLieux/{id}', name: 'listeLieux')]
    public function LieuxParVille(int                 $id,
                                  LieuRepository      $LieuxRepo,
                                  SerializerInterface $serializer): Response
    {

        $lieux = $LieuxRepo->findBy(["ville" => $id]);
        $productSerialized = $serializer->serialize($lieux, 'json', ['groups' => ['lieux']]);
        return new Response($productSerialized);
    }

    /**
     * Cette URL affiche une page d'informations du lieu
     *
     * @param int $id
     * @param LieuRepository $LieuxRepo
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/lieu/AfficherLieu/{id}', name: 'affLieu')]
    public function LieuxParVilleBis(int            $id,
                                     LieuRepository $LieuxRepo): Response
    {
        $lieu = $LieuxRepo->findOneBy(["id" => $id]);
        return $this->render('lieux/afficheLieu.html.twig', ["lieu" => $lieu]);
    }

    /**
     * Redirige vers la page de création du lieu, mais stocke la sortie en session avant
     * @param Request $request
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    #[isGranted("ROLE_USER")]
    #[Route('/lieu/allerLieux', name: 'allerLieux')]
    public function sortieVersLieux(Request $request, SessionInterface $session): Response
    {
        // Stocker les données dans la session
        $session->set('sortie', $request->get("sortie"));
        // Rediriger vers l'autre écran
        return $this->redirectToRoute('creer_lieu');
    }

}
