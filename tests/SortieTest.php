<?php

namespace App\Tests;

use App\Entity\Sortie;
use PHPUnit\Framework\TestCase;

class SortieTest extends TestCase
{
    public function testNomOk(): void
    {
        $sortie=new Sortie();
        $sortie->setNom("sortie cool");
        $this->assertEquals($sortie->getNom(),"sortie cool");

    }
}
