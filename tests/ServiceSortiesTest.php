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

    public function testSortieValideOk(): void
    {
        //sortie OK
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setEtat(1);
        $sortie->setLieu(new Lieu());
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,90);
       $this->assertEquals($tab["ok"],true);
        $this->assertEquals($tab["message"],"");
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
        //nb d'inscription incohérent trop grand
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(3000);
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"Le nombre de participants doit être compris entre 0 et 1000.");
    }

    public function testSortieValideKo3(): void
    {
        //nb d'inscription incohérent trop petit et lieu obligatoire
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(0);
        $sortie->setEtat(2);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"Le nombre de participants doit être compris entre 0 et 1000.Le lieu est obligatoire.");
    }
    public function testSortieValideKo4(): void
    {
        //date limite antérieure à date de sortie
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 3000 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"La date limite d'inscription n'est pas valide.");
    }

    public function testSortieValideKo5(): void
    {
        //date de sortie =date du jour
        $sortie=new Sortie();
        $sortie->setDebutSortie( new \DateTime('now'));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' - 10 minutes'))));
        $service = new SortiesService();
        $tab=$service->verifSortieValide($sortie,31);
        $this->assertEquals($tab["ok"],false);
        $this->assertEquals($tab["message"],"La sortie doit commencer dans 4 heures au moins.La date limite d'inscription n'est pas valide.");
    }
}
