<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function test(): Response
    {
       if ($this->getUser())
       return $this->redirectToRoute("sorties_liste");
       else
        return   $this->redirectToRoute("app_login");
    }

}
