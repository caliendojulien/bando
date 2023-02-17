<?php

namespace App\Controller;
use App\Entity\EtatSorties;
use App\Entity\Sortie;
use App\Form\SortieAnnulationFormType;
use App\Form\SortieFormType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Form\SortieSearchFormType;
use App\Repository\SortieRepository;
use App\Repository\StagiaireRepository;
use App\Repository\VilleRepository;
use App\Services\InscriptionsService;
use App\Services\SortiesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sorties', name: 'sorties')]
class SortiesController extends AbstractController
{
    #[isGranted("ROLE_USER")]
    #[Route('/liste', name: '_liste')]
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

    #[isGranted("ROLE_USER")]
    #[Route('/sortie/{id}', name: '_detail')]
    public function detail(
        int              $id,
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
     * @param SortiesService $serviceSorties
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/creer', name: '_creer')]
    public function creer(
        EntityManagerInterface $entityManager,
        StagiaireRepository $stagRepo,
        VilleRepository $villesRepo,
        LieuRepository $LieuxRepo,
        Request $request,
        SortiesService $serviceSorties
    ): Response {

        //récupération du stagiaire connecté
        $user=$stagRepo->findOneAvecCampus($this->getUser()->getUserIdentifier());

        //initialisation de la sortie
        $sortie = new Sortie();
        $sortie->setOrganisateur($user);
        //mettre le campus de l'organisateur par défaut au début
        if (!$sortie->getCampus()) $sortie->setCampus($user->getCampus());

        //création du formulaire
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);

        //traiter l'envoi du formulaire
        if ($form->isSubmitted()) {

            // renseigner le lieu
            $idLieu = $request->request->get("choixLieux");
            if ($idLieu) {
                $lieu = $LieuxRepo->findOneBy(["id" => $idLieu]);
                $sortie->setLieu($lieu);
            }

             //  trouver la date de fin en fonction de la durée et de la date de début
            $duree = $request->request->get("duree");
            settype($duree, 'integer');
            $serviceSorties->ajouterDureeAdateFin($sortie,$duree);

            //l'état dépend du bouton sur lequel on a cliqué
            If ($request->request->get( 'Publier' ))
                $sortie->setEtat(EtatSorties::Publiee->value);//la sortie est à l'état "publiée"
            else
                $sortie->setEtat(EtatSorties::Creee->value);//la sortie est à l'état "créée"

            //vérification des contraintes métier
            $metier=  $serviceSorties->verifSortieValide($sortie,$duree);

            //si OK on enregistre
            if ($form->isValid() && $metier["ok"]) {
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('sorties_liste');
            }
            else //sinon on reste sur la page mais on affiche les erreurs
            {
                $this->addFlash("error","merci de vérifier, il y a des erreurs.".$metier["message"]);
            }
        }

        //passer la liste des villes au formulaire
        $villes = $villesRepo->findAll();
        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),
            "villes" => $villes
        ]);
    }

    /**
     * Méthode permettant à un utilisateur authentifié de s'inscrire à une sortie
     * @param int $idSortie L'identifiant de la sortie
     * @param SortieRepository $sortieRepo
     * @param EntityManagerInterface $entityManager
     * @param InscriptionsService $serv
     * @return Response
     */
//    #[Route('/sinscrire/{idSortie}/{idStagiaire}', name: 'sorties_sinscrire')]
    #[isGranted("ROLE_USER")]
    #[Route('/sinscrire/sortie/{idSortie}', name: '_sinscrire')]

    public function Sinscrire(  int $idSortie,
//                                int $idStagiaire,
                                SortieRepository $sortieRepo,
                                EntityManagerInterface $entityManager,
                                InscriptionsService $serv) :Response
    {
        // récupérer le stagiaire ( c'est toujours le user connecté ??)
//        $stag = $stagRepo->findOneBy(["id"=>$idStagiaire]);
        $stag = $this->getUser();
        // récupérer la sortie
        $sortie=$sortieRepo->findOneBy(["id"=>$idSortie]);
        // inscrire et confirmer ou infirmer l'inscription

        $tab=$serv->inscrire($stag,$sortie,$entityManager);
      if ($tab[0])
          $this->addFlash('success', 'vous avez été inscrit à la sortie');
      else  $this->addFlash('error','inscription impossible : '.$tab[1]);

        //rediriger
        return $this->redirectToRoute('sorties_liste');
    }

    /**
     * Méthode permettant à un utilisateur authentifié de se désister d'une sortie à laquelle il est inscrit.
     *
     * @param int $id L'identifiant de la sortie.
     * @param SortieRepository $sortieRepository Le repository des sorties.
     * @param EntityManagerInterface $entityManager L'EntityManager pour gérer les entités Sortie.
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/desistement/sortie/{id}', name: '_desistement')]
    public function desistement(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupère la sortie correspondant à l'ID spécifié.
        $sortie = $sortieRepository->findOneBy(["id" => $id]);

        // Vérifie que l'utilisateur actuel participe bien à la sortie.
        $user = $this->getUser();
        $participants = $sortie->getParticipants();
        foreach ($participants as $participant) {
            if ($participant === $user) {
                // Si l'utilisateur participe bien à la sortie, le supprime de la liste des participants.
                $sortie->removeParticipant($participant);
                $entityManager->flush();
                // Ajoute un message flash pour indiquer que le désistement a été enregistré.
                $this->addFlash('success', 'Votre désistement a bien été pris en compte.');
            }
        }

        // Redirige l'utilisateur vers la liste des sorties.
        return $this->redirectToRoute('sorties_liste');
    }

    #[isGranted("ROLE_USER")]
    #[Route('/annulation/sortie/{id}', name: '_annulation')]
    public function annulation(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager,StagiaireRepository $stagiaireRepository,VilleRepository $villeRepository,LieuRepository $lieuRepository,CampusRepository $campusRepository,Request $request): Response
    {
        // Récupère la sortie correspondant à l'ID spécifié.
        $sortie = $sortieRepository->findOneBy(["id" => $id]);

        //Récupérer le campus associé à la sortie
        $campus = $campusRepository->findOneBy(['id'=> $sortie->getCampus()]);

        //Récupérer le lieu associé à la sortie
        $lieu = $lieuRepository->findOneBy(['id'=>$sortie->getLieu()->getId()]);

        //Récupérer la ville associé à la sortie
        $ville = $villeRepository->findOneBy(['id'=>$lieu->getVille()->getId()]);

        //Récupère l'id du stagiaire connecté
        $stagiaireConnecte = $this->getUser();
        $stagiaire = $stagiaireRepository->findOneBy(['email' => $stagiaireConnecte->getUserIdentifier()]);
        $sortieForm = $this->createForm(SortieAnnulationFormType::class, $sortie);
        $sortieForm->handleRequest($request);

        //Vérifie si l'id de l'organisateur correspond à l'id de l'utilisateur connecté, si la date de début sortie n'est pas dépassée , si se n'est le cas il est renvoyé vers la liste des sorties
        if($sortie->getOrganisateur()->getId() != $stagiaire->getId() || $sortie->getEtat() > 3){
            return $this->redirectToRoute('sorties_liste');
        }
        if ($sortieForm->isSubmitted()) {
            $sortie->setEtat(6);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sorties_liste');
        }

        return $this->render('sorties/annulation.html.twig', [
            'sortieForm' => $sortieForm,
            'sortie' => $sortie,
            'campus' => $campus,
            'lieu' => $lieu,
            'ville' => $ville
        ]);
    }

}
