<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LieuController extends AbstractController
{

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
