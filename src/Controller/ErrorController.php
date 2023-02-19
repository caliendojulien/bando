<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/erreur', name: '_erreur')]
class ErrorController extends AbstractController
{
    #[Route('/acces-refuse', name: '_acces-refuse')]
    public function accessDenied(): Response
    {
        return $this->render('erreur/acces_refuse.html.twig')->setStatusCode(Response::HTTP_FORBIDDEN);
    }
}
