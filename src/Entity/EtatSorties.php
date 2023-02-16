<?php

namespace App\Entity;

/**
 * Enumération
 */
enum EtatSorties:int
{
    case Creee=1;
    case Publiee=2;
    case Cloturee=3;
    case EbCours=4;
    case Passee=5;
    Case Annulee=6;
    case Archivee=7;
    case Complet=8;

}
