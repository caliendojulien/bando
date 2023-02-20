<?php

namespace App\Tests;

use App\Entity\Campus;
use App\Entity\Stagiaire;
use PHPUnit\Framework\TestCase;

class StagiaireTest extends TestCase
{
    public function testGetId(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getId());
    }

    public function testSetGetEmail(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getEmail());

        $stagiaire->setEmail('test@test.com');
        $this->assertSame('test@test.com', $stagiaire->getEmail());

    }

    public function testSetGetNom(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getNom());

        $stagiaire->setNom('Dupont');
        $this->assertSame('Dupont', $stagiaire->getNom());
    }

    public function testSetGetPrenom(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getPrenom());

        $stagiaire->setPrenom('Jean');
        $this->assertSame('Jean', $stagiaire->getPrenom());
    }

    public function testSetGetTelephone(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getTelephone());

        $stagiaire->setTelephone('0123456789');
        $this->assertSame('0123456789', $stagiaire->getTelephone());

        $this->assertTrue(preg_match('/^\d{10}$/', $stagiaire->getTelephone()) === 1);
    }

    public function testSetGetUrlPhoto(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getUrlPhoto());

        $stagiaire->setUrlPhoto('https://example.com/photo.png');
        $this->assertSame('https://example.com/photo.png', $stagiaire->getUrlPhoto());
    }

    public function testIsSetAdministrateur(): void
    {
        $stagiaire = new Stagiaire();
        $stagiaire->setAdministrateur(true);
        $this->assertTrue($stagiaire->isAdministrateur());
    }

    public function testIsSetActif(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->isActif());

        $stagiaire->setActif(true);
        $this->assertTrue($stagiaire->isActif());
    }

    public function testIsSetPremiereConnexion(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->isPremiereConnexion());

        $stagiaire->setPremiereConnexion(true);
        $this->assertTrue($stagiaire->isPremiereConnexion());
    }

    public function testGetSetCampus(): void
    {
        $stagiaire = new Stagiaire();
        $this->assertNull($stagiaire->getCampus());

        $campus = new Campus();
        $campus->setNom('Campus Test');

        // Vérification de la liaison entre le stagiaire et le campus
        $this->assertInstanceOf(Stagiaire::class, $stagiaire->setCampus($campus));
        $this->assertInstanceOf(Campus::class, $stagiaire->getCampus());
        $this->assertSame('Campus Test', $stagiaire->getCampus()->getNom());

        // Vérification que le stagiaire peut être dissocié du campus
        $this->assertInstanceOf(Stagiaire::class, $stagiaire->setCampus(null));
        $this->assertNull($stagiaire->getCampus());
    }
}

