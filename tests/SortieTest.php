<?php

namespace App\Tests;

use App\Entity\Lieu;
use App\Entity\Sortie;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class SortieTest extends TestCase
{
    public function testNomOk(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortie cool");
        $this->assertEquals($sortie->getNom(),"sortie cool");
    }

    public function testNomKo(): void
    {
        $sortie=new Sortie();
        $sortie->setEtat(5);
        $sortie->setLieu(new Lieu());
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));

        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($sortie);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
        // J'ajoute un nom
        $sortie->setNom("sortieA");

        // Je relance la validation
        $errors = $validator->validate($sortie);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testEtatOk(): void
    {
        $sortie=new Sortie();
        $sortie->setEtat(2);
        $this->assertEquals($sortie->getEtat(),2);
    }
    public function testEtatko(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortieA");
        $sortie->setEtat(10);
        $sortie->setLieu(new Lieu());
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));

        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($sortie);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
        // J'ajoute un etat
        $sortie->setEtat(2);

        // Je relance la validation
        $errors = $validator->validate($sortie);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testDebutOk(): void
    {
        $sortie=new Sortie();
        $date =  new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes')));

        $sortie->setDebutSortie($date);
        $this->assertEquals($sortie->getDebutSortie(),$date);
    }
    public function testDebutKo(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortieA");
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' - 20 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));

        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($sortie);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
        // J'ajoute un debut valide
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));

        // Je relance la validation
        $errors = $validator->validate($sortie);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testLimiteOk(): void
    {
        $sortie=new Sortie();
        $date =  new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes')));

        $sortie->setDateLimiteInscription($date);
        $this->assertEquals($sortie->getDateLimiteInscription(),$date);
    }
    public function testLimiteKo(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortieA");
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(10);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1500 minutes'))));

        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($sortie);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
        // J'ajoute un debut valide
       $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));

        // Je relance la validation
        $errors = $validator->validate($sortie);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testnbinscriptionOk(): void
    {
        $sortie=new Sortie();

        $sortie->setNombreInscriptionsMax(10);
        $this->assertEquals($sortie->getNombreInscriptionsMax(),10);
    }
    public function testnbinscriptionKo(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortieA");
        $sortie->setEtat(2);
        $sortie->setLieu(new Lieu());
        $sortie->setDebutSortie( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1440 minutes'))));
        $sortie->setNombreInscriptionsMax(0);
        $sortie->setDateLimiteInscription( new \DateTime(date('Y-m-d H:i:s', strtotime((new \DateTime())->format("Y-m-d H:i:s") . ' + 1300 minutes'))));

        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($sortie);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
        // J'ajoute un debut valide
        $sortie->setNombreInscriptionsMax(10);
        // Je relance la validation
        $errors = $validator->validate($sortie);
        // Ca doit passer
        $this->assertCount(0, $errors);
        $sortie->setNombreInscriptionsMax(50000);
        // Elle ne doit pas l'être
        $this->assertNotEquals(0, $errors);
    }
}
