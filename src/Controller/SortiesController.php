<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\LieuRepository;
use App\Form\SortieSearchFormType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\StagiaireRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SortiesController extends AbstractController
{

    #[Route('/', name: '_sorties_test')]
    public function test(): Response
    {
        return $this->render('sorties/test.html.twig', []);
    }

    #[Route('/sorties', name: '_sorties')]
    public function sorties(
        SortieRepository     $sortieRepository,
        Request              $request,
        FormFactoryInterface $formFactory
    ): Response
    {
        // Création du formulaire de recherche de sorties
        $form = $formFactory->create(SortieSearchFormType::class);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = [
                'nom' => $form->get('nom')->getData(),
                'debutSortie' => $form->get('debutSortie')->getData(),
                'finSortie' => $form->get('finSortie')->getData(),
                'campus' => $form->get('campus')->getData(),
                'organisateur' => $form->get('organisateur')->getData(),
                'inscrit' => $form->get('inscrit')->getData(),
                'non_inscrit' => $form->get('non_inscrit')->getData(),
                'sorties_passees' => $form->get('sorties_passees')->getData()
            ];

            // Si la case "Sorties passées" est cochée, on ignore la date de début de la sortie
            if ($data['sorties_passees']) {
                $data['debutSortie'] = null;
            }

            // Recherche des sorties en fonction des données renseignées par l'utilisateur
            $sorties = $sortieRepository->findSorties(
                $data['nom'],
                $data['debutSortie'],
                $data['finSortie'],
                $data['campus'],
                $data['organisateur'],
                $this->getUser(),
                $data['inscrit'],
                $data['non_inscrit'],
                $data['sorties_passees']
            );
        } else {
            // Si le formulaire n'a pas été soumis ou n'est pas valide, récupération de toutes les sorties
            $sorties = $sortieRepository->findSorties();
        }

        // Rendu de la vue et envoi des données
        return $this->render('sorties/sorties.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sortie/{id}', name: '_sortie')]
    public function detail(
        int            $id,
        SortieRepository $sortieRepository
    ): Response
    {
        $sortie = $sortieRepository->findOneBy(["id" => $id]);
        return $this->render('sorties/sortie-detail.html.twig',
            compact('sortie')
        );
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
