<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;

class InscriptionsService
{
    function inscrire(Stagiaire $stag,Sortie $sortie,EntityManagerInterface $em):bool{
        // Vérifier qu'il est possible de s'inscrire à la sortie
        // la sortie doit être à l'état 2
        // le nb d'inscrits ne dépasse pas le nb max d'inscription
        // le stagiaire ne doit pas déjà être inscrit
        if ($sortie->getEtat()==\App\Entity\EtatSorties::Publiee->value &&
            $sortie->getParticipants()->count() < $sortie->getNombreInscriptionsMax() &&
            !$sortie->getParticipants()->contains($stag))
        {
            // inscrire
            $sortie->addParticipant($stag);
            // persister
             $em->persist($sortie);
             $em->flush();
        }
        else return false;
        return true;
    }

}