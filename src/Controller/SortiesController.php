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

    /**
     * @param EntityManagerInterface $entityManager
     * @param StagiaireRepository $stagRepo
     * @param VilleRepository $villesRepo
     * @param LieuRepository $LieuxRepo
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
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
        $sortie->setEtat(1);//la sortie est à l'état "créée"
        $sortie->setOrganisateur($user);
        //mettre le campus de l'organisateur par défaut
        if (! $sortie->getCampus()) {
            $sortie->setCampus($user->getCampus());
        }
        //création du formulaire
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);

        //traiter l'envoi du formulaire
         if ($form->isSubmitted() ) {
             //  trouver la date de fin en fonction de la durée et de la date de début
            $duree = $request->request->get("duree");
            settype($duree,'integer');
            if ($duree){
                $dateFin=new \DateTime($sortie->getDebutSortie()->format("d/m/y H:i"));
                $dateFin= $dateFin->add(new \DateInterval('PT'.$duree.'M'));
                    $sortie->setFinSortie( $dateFin);
            }
             // renseigner le lieu
            $idLieu=$request->request->get("choixLieux");
            $lieu=$LieuxRepo->findOneBy(["id"=>$idLieu]);
            $sortie->setLieu($lieu);
            //si OK on enregistre
            if ($form->isValid()) {
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('_sorties');
            }
        }

        //passer la liste des villes au formulaire
         $villes=$villesRepo->findAll();
        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),
            "villes"=>$villes
        ]);
    }

    /**
     * Cette URL permet de racupérer les lieux d'une ville
     *
     * @param int $id L'identifiant de la ville
     * @param LieuRepository $LieuxRepo
     * @param SerializerInterface $serializer
     * @return Response Json contenant les lieux
     */
    #[Route('/listerLieux/{id}', name: 'sorties_listeLieux')]
    public function LieuxParVille(int $id,
                                  LieuRepository $LieuxRepo,
                                 SerializerInterface $serializer):Response{
        $lieux=$LieuxRepo->findBy(["ville"=>$id]) ;
        $productSerialized = $serializer->serialize($lieux, 'json',['groups' => ['lieux']]);
        return new Response($productSerialized);
    }

}
