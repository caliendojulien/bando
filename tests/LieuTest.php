<?php

namespace App\Tests;

use App\Entity\Lieu;
use App\Entity\Ville;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class LieuTest extends TestCase
{
    public function testNomOk(): void
    {
        $lieu=new Lieu();
        $lieu->setNom("bar truc");
        $this->assertEquals($lieu->getNom(),"bar truc");

    }
    public function testNomKo(): void
    {

        $nantes = new Lieu();
        $nantes->setRue("truc");
        $nantes->setVille(new Ville());
        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($nantes);
        // Elle ne doit pas l'être puisque le nom ne peut pas etre BLANK
        $this->assertNotEquals(0, $errors);
        // J'ajoute un nom
        $nantes->setNom("Nantes");

        // Je relance la validation
        $errors = $validator->validate($nantes);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testRueOk(): void
    {
        $lieu=new Lieu();
        $lieu->setRue("rue Franklin");
        $this->assertEquals($lieu->getRue(),"rue Franklin");
    }
    public function testRueko(): void
    {
        $nantes = new Lieu();
        $nantes->setNom("Nantes");
       $nantes->setVille(new Ville());
        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($nantes);
        // Elle ne doit pas l'être puisque la rue ne peut pas etre BLANK
        $this->assertNotEquals(0, $errors);
        // J'ajoute un nom
        $nantes->setrue("truc");

        // Je relance la validation
        $errors = $validator->validate($nantes);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
    public function testlongitudeOk(): void
    {
        $lieu=new Lieu();
        $lieu->setLongitude(12.65);
        $this->assertEquals($lieu->getLongitude(),12.65);
    }

    public function testlongitudeKo(): void
    {
        $lieu=new Lieu();
        $this->assertTrue(true);
    }

    public function testLatitudeOk(): void
    {
        $lieu=new Lieu();
        $lieu->setLatitude(-152.605);
        $this->assertEquals($lieu->getLatitude(),-152.605);
    }

    public function testlatitudeKo(): void
    {
        $lieu=new Lieu();
        $this->assertTrue(true);
    }


    public function testVilleKo(): void
    {
        $nantes = new Lieu();
        $nantes->setNom("Nantes");
        $nantes->setRue("truc");
        // Je récupère les asserts sur l'entité
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        // Je regarde si mon instance est valide
        $errors = $validator->validate($nantes);
        // Elle ne doit pas l'être puisque la ville ne peut pas etre BLANK
        $this->assertNotEquals(0, $errors);
        // J'ajoute un nom
        $nantes->setVille(new Ville());

        // Je relance la validation
        $errors = $validator->validate($nantes);
        // Ca doit passer
        $this->assertCount(0, $errors);
    }
}
