<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/erreur', name: '_erreur')]
    public function show(FlattenException $exception): Response
    {
        $message = match ($exception->getStatusCode()) {
            401 => 'Accès non autorisé : Vous n\'êtes pas autorisé à accéder à cette page. Veuillez vous connecter pour continuer.',
            403 => 'Accès interdit : L\'accès à cette page est interdit. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter l\'administrateur du site.',
            404 => 'Page non trouvée : La page que vous recherchez n\'a pas été trouvée. Vérifiez l\'URL et réessayez.',
            500 => 'Erreur interne du serveur : Une erreur interne est survenue sur le serveur. Veuillez réessayer plus tard ou contacter l\'administrateur du site pour plus d\'informations.',
            502 => 'Le serveur a rencontré une erreur Veuillez réessayer plus tard.',
            503 => 'Service non disponible : Le service est temporairement indisponible. Veuillez réessayer plus tard.',
            504 => 'Le serveur a pris trop de temps à répondre. Veuillez réessayer plus tard.',
            default => 'Erreur inattendue'
        };

        return $this->render(
            'bundles/TwigBundle/Exception/error.html.twig',
            ['message' => $message, 'statusCode' => $exception->getStatusCode()]
        );
    }

}
