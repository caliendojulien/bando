<?php

namespace App\Controller;

use App\Entity\EtatSortiesEnum;
use App\Entity\Sortie;
use App\Form\SortieAnnulationFormType;
use App\Form\SortieFormType;
use App\Form\SortieSearchFormType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\StagiaireRepository;
use App\Repository\VilleRepository;
use App\Services\EtatSorties;
use App\Services\InscriptionsService;
use App\Services\MailService;
use App\Services\SortiesService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method AccessDeniedException(string $string)
 */
#[Route('/sorties', name: 'sorties')]
class SortiesController extends AbstractController
{

    #[isGranted("ROLE_USER")]
    #[Route('/liste', name: '_liste')]
    public function sorties(
        SortieRepository     $sortieRepository,
        Request              $request,
        FormFactoryInterface $formFactory,
        PaginatorInterface   $paginator,
        SessionInterface     $session,
    ): Response
    {
        try {
            // Création du formulaire de recherche de sorties
            $form = $this->createForm(SortieSearchFormType::class);
            //Récupération de l'id de l'utilisateur connecté
            $stagiaire = $this->getUser();
            // Gestion de la soumission du formulaire
            $form->handleRequest($request);
            // Récupération des données du formulaire
            $data = [
                'nom' => $form->get('nom')->getData(),
                'debutSortie' => $form->get('debutSortie')->getData(),
                'finSortie' => $form->get('finSortie')->getData(),
                'campus' => $form->get('campus')->getData(),
                'organisateur' => $form->get('organisateur')->getData(),
                'inscrit' => $form->get('inscrit')->getData(),
                'sorties_ouvertes' => $form->get('sorties_ouvertes')->getData()
            ];
            $session->set('debutSortie', $form->get('debutSortie')->getData());

            // Si la case "Sorties passées" est cochée, on ignore la date de début de la sortie
            if ($data['sorties_ouvertes']) {
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
                $data['sorties_ouvertes']
            );
            $sortiesPaginee = $paginator->paginate(
                $sorties,
                $request->query->getInt('page', 1), 30);

            // Rendu de la vue et envoi des données
            return $this->render('sorties/sorties.html.twig', [
                'sorties' => $sortiesPaginee,
                'form' => $form->createView(),
                'user_connecte' => $stagiaire
            ]);
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

    #[isGranted("ROLE_USER")]
    #[Route('/sortie/{id}', name: '_detail')]
    public function detail(
        int              $id,
        SortieRepository $sortieRepository
    ): Response
    {
        try {
            $sortie = $sortieRepository->findOneBy(["id" => $id]);
            return $this->render('sorties/sortie-detail.html.twig',
                compact('sortie')
            );
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
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
        StagiaireRepository    $stagRepo,
        VilleRepository        $villesRepo,
        LieuRepository         $LieuxRepo,
        Request                $request,
        SortiesService         $serviceSorties,
        SessionInterface       $session,
        LoggerInterface        $logger
    ): Response
    {
        try {

            //initialisation de la sortie
            // on va chercher la sortie en session, s'il n'y en a pas, alors on crée une nouvelle sortie
            $sortie = $session->get('sortie');
            if (!$sortie) {
                $sortie = new Sortie();
                //valeurs par défaut
                $sortie->setDebutSortie((new \DateTime('19:00:00'))->add(new \DateInterval('P2D')));
                $sortie->setDateLimiteInscription((new \DateTime('18:00:00'))->add(new \DateInterval('P1D')));
                $sortie->setNombreInscriptionsMax(5);
            }
            $logger->debug("le user " . $this->getUser()->getUserIdentifier() . " a créé cette sortie " . $sortie->getNom());
            return $this->creerOuModifierSortie($entityManager,
                $villesRepo,
                $LieuxRepo,
                $request,
                $stagRepo,
                $serviceSorties,
                true // True car on est en mode création
                , $sortie);

        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

    /**
     * Modifie une sortie existante dans la base de données.
     *
     * @param int $id L'identifiant de la sortie à modifier.
     * @param EntityManagerInterface $entityManager L'entité manager pour accéder à la base de données.
     * @param VilleRepository $villesRepo Le repository pour accéder aux villes de la base de données.
     * @param LieuRepository $LieuxRepo Le repository pour accéder aux lieux de la base de données.
     * @param Request $request
     * @param StagiaireRepository $stagRepo
     * @param SortiesService $serviceSorties
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/modifier/{id}', name: '_modifier-sortie')]
    public function modifier(
        int                    $id,
        EntityManagerInterface $entityManager,
        VilleRepository        $villesRepo,
        LieuRepository         $LieuxRepo,
        Request                $request,
        StagiaireRepository    $stagRepo,
        SortiesService         $serviceSorties,
    ): Response
    {
        try {
            // Récupère l'entité Sortie correspondant à l'ID passé en paramètre de la requête
            $sortie = $entityManager->getRepository(Sortie::class)->find($id);
            if (!$sortie) throw $this->createNotFoundException('La sortie n\'existe pas.');
            if ($sortie->getEtat() != EtatSortiesEnum::Creee->value) throw new Exception("Cette sortie n'est pas modifiable");
            if ($this->getUser() !== $sortie->getOrganisateur())
                throw $this->createAccessDeniedException('Vous ne pouvez pas modifier une sortie dont vous n\'êtes pas l\'organisateur.');
            return $this->creerOuModifierSortie($entityManager,
                $villesRepo,
                $LieuxRepo,
                $request,
                $stagRepo,
                $serviceSorties,
                false // false car on est en mode modification
                , $sortie);

        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

    /**
     * Fonction appelée lors de ld création et lors de la modif d'une sortie, code commun aux deux
     * @param EntityManagerInterface $entityManager
     * @param VilleRepository $villesRepo
     * @param LieuRepository $LieuxRepo
     * @param Request $request
     * @param StagiaireRepository $stagRepo
     * @param SortiesService $serviceSorties
     * @param bool $cree Si true, on est en mode création, sinon en modification
     * @param Sortie $sortie La sortie ayant, soit des param par défaut si création, soit des valeurs issues de la BDD si modification
     * @return Response
     * @throws Exception
     */
    private function creerOuModifierSortie(
        EntityManagerInterface $entityManager,
        VilleRepository        $villesRepo,
        LieuRepository         $LieuxRepo,
        Request                $request,
        StagiaireRepository    $stagRepo,
        SortiesService         $serviceSorties,
        bool                   $cree,
        Sortie                 $sortie
    ): Response
    {
        //récupération du stagiaire connecté
        $user = $stagRepo->findOneAvecCampus($this->getUser()->getUserIdentifier());

        $sortie->setOrganisateur($user);
        //mettre le campus de l'organisateur par défaut au début
        if (!$sortie->getCampus()) $sortie->setCampus($user->getCampus());

        //création du formulaire
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if (!$cree) {
            $interval = $sortie->getDebutSortie()->diff($sortie->getFinSortie());
            $duree = $interval->d * 1440 + $interval->h * 60 + $interval->i;
        } else $duree = 30;

        //traiter l'envoi du formulaire
        if ($form->isSubmitted()) {

            // renseigner le lieu
            $idLieu = $request->request->get("choixLieux");
            if ($idLieu) $sortie->setLieu($LieuxRepo->findOneBy(["id" => $idLieu]));

            //  trouver la date de fin en fonction de la durée et de la date de début
            $duree = (int)$request->request->get("duree");
            $serviceSorties->ajouterDureeAdateFin($sortie, $duree);

            //l'état dépend du bouton sur lequel on a cliqué
            if ($request->request->get('Publier'))
                $sortie->setEtat(($sortie->getEtat() == EtatSortiesEnum::Publiee->value) ?
                    EtatSortiesEnum::Creee->value : EtatSortiesEnum::Publiee->value);
            else
                $sortie->setEtat(EtatSortiesEnum::Creee->value);

            //vérification des contraintes métier
            $metier = $serviceSorties->verifSortieValide($sortie, $duree);


            //si OK on enregistre
            if ($form->isValid() && $metier["ok"]) {
                if ($request->request->get('Supprimer'))
                    $entityManager->remove($sortie);
                else
                    $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('sorties_liste');
            } else //sinon on reste sur la page mais on affiche les erreurs
            {
                $this->addFlash("error", "Merci de vérifier, il y a des erreurs. " . $metier["message"]);
            }
        }
        //passer la liste des villes au formulaire
        $villes = $villesRepo->findAll();

        // passer une liste de lieux
        //si la sortie a déjà un lieu...(on est dans modifier ou on est dans retour d'une création de lieu)
        $lelieu = $sortie->getLieu();
        //je valorise la ville aussi
        if ($lelieu) $ville = $lelieu->getVille();
        else // sinon je prends la premiere ville de la liste
            if (count($villes) > 0) $ville = $villes[0];
        if ($ville) $lieux = $LieuxRepo->findBy(["ville" => $ville]); // et je récupère les lieux de la ville, quelle qu'elle soit
        return $this->render('sorties/creer.html.twig', [
            'form' => $form->createView(),
            "villes" => $villes,
            "lieux" => $lieux,
            "modecree" => $cree,
            "laville" => $ville,
            "leLieu" => $sortie->getLieu(),
            "duree" => $duree,
            "etat" => $sortie->getEtat()
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
    #[isGranted("ROLE_USER")]
    #[Route('/sinscrire/sortie/{id}', name: '_sinscrire')]
    public function Sinscrire(int                    $id,

                              SortieRepository       $sortieRepo,
                              EntityManagerInterface $entityManager,
                              InscriptionsService    $serv): Response
    {

        try {
            // récupérer le stagiaire
            // $stag = $stagRepo->findOneBy(["id"=>$idStagiaire]);
            $stag = $this->getUser();
            // récupérer la sortie
            $sortie = $sortieRepo->findOneBy(["id" => $id]);
            // inscrire et confirmer ou infirmer l'inscription

            $tab = $serv->inscrire($stag, $sortie, $entityManager);
            if ($tab[0])
                $this->addFlash('success', 'Vous avez été inscrit à la sortie');
            else  $this->addFlash('error', 'Inscription impossible : ' . $tab[1]);

            //rediriger
            return $this->redirectToRoute('sorties_liste');

        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }


    /**
     * Méthode permettant à un utilisateur authentifié de se désister d'une sortie à laquelle il est inscrit.
     *
     * @param int $id L'identifiant de la sortie.
     * @param SortieRepository $sortieRepository Le repository des sorties.
     * @param EntityManagerInterface $entityManager L'EntityManager pour gérer les entités Sortie.
     * @param InscriptionsService $inscriptionsService
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/desistement/sortie/{id}', name: '_desistement')]
    public function desistement(int                    $id,
                                SortieRepository       $sortieRepository,
                                EntityManagerInterface $entityManager,
                                InscriptionsService    $inscriptionsService): Response
    {
        try {
            // Récupère la sortie correspondant à l'ID spécifié.
            $sortie = $sortieRepository->findOneBy(["id" => $id]);

            //récupère le participant
            $user = $this->getUser();

            if ($inscriptionsService->SeDesinscrire($user, $sortie, $entityManager))
                $this->addFlash('success', 'Votre désistement a bien été pris en compte.');
            else
                $this->addFlash('error', 'Problème lors de votre désistement, veuillez contacter un administrateur.');


            // Redirige l'utilisateur vers la liste des sorties.
            return $this->redirectToRoute('sorties_liste');
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

    /**
     * @param int $id
     * @param SortieRepository $sortieRepository
     * @param EntityManagerInterface $entityManager
     * @param StagiaireRepository $stagiaireRepository
     * @param VilleRepository $villeRepository
     * @param LieuRepository $lieuRepository
     * @param CampusRepository $campusRepository
     * @param Request $request
     * @param MailService $mailer
     * @return Response
     */
    #[isGranted("ROLE_USER")]
    #[Route('/annulation/sortie/{id}', name: '_annulation')]
    public function annulation(int                    $id,
                               SortieRepository       $sortieRepository,
                               EntityManagerInterface $entityManager,
                               StagiaireRepository    $stagiaireRepository,
                               VilleRepository        $villeRepository,
                               LieuRepository         $lieuRepository,
                               CampusRepository       $campusRepository,
                               Request                $request,
                               MailService            $mailer): Response
    {
        try {
            //Contrôle de l'id organisateur entrant
            if (!is_int($id)) {
                $this->addFlash('erreur', 'Erreur l\'utilisateur n\'est pas reconnu');
                return $this->redirectToRoute('sorties_liste');
            }

            // Récupère la sortie correspondant à l'ID spécifié.
            $sortie = $sortieRepository->findOneBy(['id' => $id]);

            //Récupérer le campus associé à la sortie
            $campus = $campusRepository->findOneBy(['id' => $sortie->getCampus()]);

            //Récupérer le lieu associé à la sortie
            $lieu = $lieuRepository->findOneBy(['id' => $sortie->getLieu()->getId()]);

            //Récupérer la ville associé à la sortie
            $ville = $villeRepository->findOneBy(['id' => $lieu->getVille()->getId()]);

            //Récupère l'id du stagiaire connecté
            $stagiaireConnecte = $this->getUser();
            $stagiaire = $stagiaireRepository->findOneBy(['email' => $stagiaireConnecte->getUserIdentifier()]);
            $sortieForm = $this->createForm(SortieAnnulationFormType::class, $sortie);
            $sortieForm->handleRequest($request);


            //Vérifie si l'id de l'organisateur correspond à l'id de l'utilisateur connecté, si la date de début sortie n'est pas dépassée , si se n'est le cas il est renvoyé vers la liste des sorties
            if ($sortie->getOrganisateur()->getId() != $stagiaire->getId() || $sortie->getEtat() > 3) {
                return $this->redirectToRoute('sorties_liste');

            }
            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                //on envoie un mail à chaque participant
                $mailer->sendMailParticipants($sortie, "Une sortie a été annulée");
                foreach ($sortie->getParticipants() as $value) {
                    $sortie->removeParticipant($value);
                }
                $sortie->setEtat(EtatSortiesEnum::Annulee->value);
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
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

    #[isGranted("ROLE_USER")]
    #[Route('/updataEtat', name: '_updateEtat')]
    public function miseAjourEtat(Request     $request,
                                  EtatSorties $etatSorties)
    {
        try {
            $etatSorties->updateEtatSorties();

        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
        // Redirige l'utilisateur vers la liste des sorties.
        return $this->redirectToRoute('sorties_liste');
    }

}
