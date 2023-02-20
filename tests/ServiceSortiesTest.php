<?php

namespace App\Tests;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Stagiaire;
use App\Repository\SortieRepository;
use App\Services\SortiesService;
use DateInterval;
use PHPUnit\Framework\TestCase;

class ServiceSortiesTest extends TestCase
{
    public function testAjoutDuree(): void
    {
        $sortie=new Sortie();
         $service = new SortiesService();
        $sortie->setDebutSortie(new \DateTime());
        $dateFin = new \DateTime(date('Y-m-d H:i:s', strtotime($sortie->getDebutSortie()->format("Y-m-d H:i:s") . ' + 90 minutes')));
        $service->ajouterDureeAdateFin($sortie,90);
        $this->assertEquals($dateFin,$sortie->getFinSortie());
    }
    public function testSortieValideKo1(): void
    {
        $sortie=new Sortie();
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,20);
        $this->assertEquals($tab["ok"],false);
    }
    public function testSortieValideKo2(): void
    {
        //nb d'inscription incohérent
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 350 minutes'))));

        $sortie->setNombreInscriptionsMax(3000);
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 150 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"La date limite d'inscription n'est pas valide.Le nombre de participants doit être compris entre 0 et 1000.");
    }

    public function testSortieValideKo3(): void
    {
        //nb d'inscription incohérent
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 350 minutes'))));

        $sortie->setNombreInscriptionsMax(0);
        $sortie->setEtat(2);
         $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 150 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"La date limite d'inscription n'est pas valide.Le nombre de participants doit être compris entre 0 et 1000.Le lieu est obligatoire.");
    }
}
