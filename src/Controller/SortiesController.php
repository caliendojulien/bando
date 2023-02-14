<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{

    #[Route('/', name: 'sorties_test')]
    public function test(): Response
    {
        return $this->render('sorties/test.html.twig', [ ]);
    }
    #[Route('/sorties', name: 'app_sorties')]
    public function sorties(): Response
    {
        return $this->render('sorties/sorties.html.twig', [
            'controller_name' => 'SortiesController',
        ]);
    }
}
