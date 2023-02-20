<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SortiesService
{
    /**
     * Vérifie qu'une sortie respecte les règles métier
     * @param Sortie $laSortie
     * @param int $duree
     * @return array retourne le tableau avec "ok" => boolean
     *                                        "message" => la liste des erreurs si pas ok
     */
    public function verifSortieValide(Sortie $laSortie,int $duree):array{
        $message="";
        $today= new \DateTime('now');
        $todayPlus4h = $today->add(new \DateInterval('PT4H'));

        //le début doit commencer au plus tôt 4H plus tard que maintenant
        $message .= ($laSortie->getDebutSortie() >= $todayPlus4h) ? "":"La sortie doit commencer dans 4 heures au moins.";
        //la date d'inscription doit être postérieure à la date du jour mais antérieure à la date de début
        $message .= ($laSortie->getDateLimiteInscription() > $today &&
                      $laSortie->getDateLimiteInscription() < $laSortie->getDebutSortie()) ?
                        "":"La date d'inscription n'est pas valide.";
        //la durée est forcément > 30min
        $message .= ($duree >= 30) ? "":"La durée doit être supérieure à 30 minutes.";
        //l'état est 1 ou 2 - créée ou publiée
        $message .= ($laSortie->getEtat() == \App\Entity\EtatSortiesEnum::Creee->value ||
                    $laSortie->getEtat() == \App\Entity\EtatSortiesEnum::Publiee->value ) ?
                    "":"La sortie ne peut pas être à l'état ". $laSortie->getEtat()."." ;
        // le nb d'inscrit est entre 1 et 1000
        $message .= ($laSortie->getNombreInscriptionsMax() > 0 && $laSortie->getNombreInscriptionsMax() <=1000) ?
                    "":"Le nombre de participants doit être compris entre 0 et 1000.";
        // le lieu est obligatoire
        $message .= ($laSortie->getLieu()) ? "":"Le lieu est obligatoire.";

        $ok = $message=="";
        return ["ok"=>$ok,"message"=>$message];
    }

    /**
     * Ajoute la durée à la date de début de la sortie
     * @param Sortie $sortie
     * @param $duree
     * @return void
     * @throws \Exception
     */
    Public function ajouterDureeAdateFin(Sortie $sortie,$duree):void{
        if ($duree) {
            $dateFin = new DateTime($sortie->getDebutSortie()->format("Y-m-d H:i:s"));
            $dateFin = $dateFin->add(new DateInterval('PT' . $duree . 'M'));
            $sortie->setFinSortie($dateFin);
        }
    }
    Public function TraiterEnvoiFormulaireCreerSortie(SortieFormType $form,
                                                      Request $request,
                                                      EntityManagerInterface $entityManager){

    }
}