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

    public function sendMailParticipants(Sortie $sortie,string $message)
    {
            foreach ($sortie->getParticipants() as $participant) {
                $email = (new Email())
                    ->from('ne_pas_repondre@bando.f')
                    ->to($participant->getEmail())
                    ->cc($sortie->getOrganisateur()->getEmail())
                    ->subject($message)
                    ->html($message . ". Sortie concernée " . $sortie->getNom().". Raison : ".$sortie->getMotifAnnulation() );
                $this->mailer->send($email);
            }

    }
}