<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * Route principale d'accueil :login ou liste des sorites
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function test(): Response
    {
        try {
            if ($this->getUser())
                return $this->redirectToRoute("sorties_liste");
            else
                return $this->redirectToRoute("app_login");
        } catch (Exception $ex) {
            return $this->render('pageErreur.html.twig', ["message" => $ex->getMessage()]);
        }
    }

}
