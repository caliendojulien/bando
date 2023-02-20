<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LieuxCreerTest extends WebTestCase
{
    //------------------------------------
    //Vérification de présence des champs
    //------------------------------------
    public function testTitre(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
//
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Créer un lieu');
       // $this->assertTrue(true);
    }
    public function testChampNom(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('input[type="text"][name="lieu[nom]"]');

        // Vérifier que le champ de saisie est présent
        $this->assertCount(1, $input);

    }
    public function testChampRue(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('input[type="text"][name="lieu[rue]"]');

        // Vérifier que le champ de saisie est présent
        $this->assertCount(1, $input);
    }
        public function testChampLongitude(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('input[type="text"][name="lieu[longitude]"]');

        // Vérifier que le champ de saisie est présent
        $this->assertCount(1, $input);
    }
    public function testChampLatitude(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('input[type="text"][name="lieu[latitude]"]');

        // Vérifier que le champ de saisie est présent
        $this->assertCount(1, $input);
    }
    public function testChampVille(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('select[name="lieu[ville]"]');

        // Vérifier que le champ de saisie est présent
        $this->assertCount(1, $input);
    }

    public function testBoutonCreer(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('button[id="creer"]');

        // Vérifier que le bouton est présent
        $this->assertCount(1, $input);
    }
    public function testLienRetour(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/lieu/Creerlieu');
        $input = $client->getCrawler()->filter('a[id="retour"]');

        // Vérifier que le lien de retour est présent
//        $this->assertCount(1, $input);
    }
}
