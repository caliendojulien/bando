<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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

    /**
     * Cette URL permet de racupérer les lieux d'une ville
     *
     * @param int $id L'identifiant de la ville
     * @param LieuRepository $LieuxRepo
     * @param SerializerInterface $serializer
     * @return Response Json contenant les lieux
     */
    #[Route('/listerLieux/{id}', name: '_listeLieux')]
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
    #[Route('/AfficherLieu/{id}', name: '_affLieu')]
    public function AfficherLieu(int $id,
                                 LieuRepository $LieuxRepo):Response{
        $lieu= $LieuxRepo->findOneBy(["id"=>$id]);
        return $this->render('lieux/afficheLieu.html.twig', [  "lieu"=>$lieu ]);
    }

}
