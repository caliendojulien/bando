<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\StagiaireRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
     StagiaireRepository $stagRepo,
        VilleRepository $villesRepo,
        LieuRepository $LieuxRepo,
        Request $request
    ): Response {

        $u = $this->getUser();
        $user=$stagRepo->findOneAvecCampus($u->getUserIdentifier());
        $sortie = new Sortie();
        //la sortie est à l'état "créée"
        $sortie->setEtat(1);
        $sortie->setOrganisateur($user);

        //mettre le campus de l'organisateur par défaut
        if (! $sortie->getCampus()) {
            $sortie->setCampus($user->getCampus());
        }
        //création du formulaire
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);

        //traiter l'envoi du formulaire
         if ($form->isSubmitted() && $form->isValid()) {
             // Todo trouver la date de fin en fonction de la durée
             //  $form->add('duree');
            // $duree = $form->get('duree');
//            $duree= $form->get("duree")->getData();
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('_sorties');
        }

        //passer la liste des villes au formulaire
         $villes=$villesRepo->findAll();
        $lieux=$LieuxRepo->findAll();
        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),
            "villes"=>$villes,
            "lieux"=>$lieux
        ]);
    }
    #[Route('/listerLieux/{id}', name: 'sorties_listeLieux')]
    public function LieuxParVille(int $id,
                                  LieuRepository $LieuxRepo,
                                 SerializerInterface $serializer):Response{
        $lieux=$LieuxRepo->findAll() ;
           // dd($lieux);
        //return new Response(json_encode($lieux));
      //  return $this->json($lieux);
        $productSerialized = $serializer->serialize($lieux, 'json',['groups' => ['lieu']]);
        return new Response($productSerialized);
        //return $this->json($productSerialized);
       // return new JsonResponse( $lieux );

//        $reponse = new JsonResponse();
//        $reponse->headers->set('Content-Type', 'application/json');
////        $reponse->setData(array('lieux' => $lieux));
//        $reponse->setData(['lieux' => $lieux]);
      //  return $reponse;

    }


}
