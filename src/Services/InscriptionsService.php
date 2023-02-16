<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;

class InscriptionsService
{

    /**
     * Méthode qui inscrit un stagiaire à une sortie
     * @param Stagiaire $stag
     * @param Sortie $sortie
     * @param EntityManagerInterface $em
     * @return array Tableau qui contient : true ou falsse en fonction de l'inscription + le message explicatif
     */
    function inscrire(Stagiaire $stag,Sortie $sortie,EntityManagerInterface $em):array{
        $message="";
        $ok=true;
        // Vérifier qu'il est possible de s'inscrire à la sortie
        // la sortie doit être à l'état 2
        if (!$sortie->getEtat()==\App\Entity\EtatSorties::Publiee->value)
            {
                $message="sortie a l'état".$sortie->getEtat();
                $ok=false;
            }
        // le nb d'inscrits ne dépasse pas le nb max d'inscription
        if( $sortie->getParticipants()->count() >= $sortie->getNombreInscriptionsMax())
            {
                $message="nb de participants max atteint";
                $ok=false;
            }
        // le stagiaire ne doit pas déjà être inscrit
        if ($sortie->getParticipants()->contains($stag)){
            $message="Vous êtes déjà inscrit !";
            $ok=false;
        }
        if ($ok)
        {
            // inscrire
            $sortie->addParticipant($stag);
            // persister
             $em->persist($sortie);
             $em->flush();
        }
        else return [false,$message];
        return [true,$message];
    }

}