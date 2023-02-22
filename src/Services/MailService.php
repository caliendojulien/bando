<?php

namespace App\Services;

use App\Entity\Sortie;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Envoi un mail à tous les participants d'une sortie
     * @param Sortie $sortie La sortie qui nécessite l'envoi d'un mail
     * @param string $message Le message en objet du mail
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendMailParticipants(Sortie $sortie, string $message): void
    {
        foreach ($sortie->getParticipants() as $participant) {
            $email = (new Email())
                ->from('ne_pas_repondre@bando.f')
                ->to($participant->getEmail())
                ->cc($sortie->getOrganisateur()->getEmail())
                ->subject($message)
                ->html("<h3>Bonjour " . $participant->getPrenom() . ". </h3> <p>" . $message . ". Sortie concernée : " . $sortie->getNom() . "</p>.<p>Raison : " . $sortie->getMotifAnnulation() . "</p>");
            $this->mailer->send($email);
        }

    }
}