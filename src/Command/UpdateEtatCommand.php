<?php

namespace App\Command;

use App\Repository\SortieRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-etat')]
class UpdateEtatCommand extends Command
{
    private $sortieRepository;
    private $entityManager;

    /**
     * @param $sortieRepository
     * @param $entityManager
     */
    public function __construct(SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Récupérer tous les sorties non archivées
        $sorties = $this->sortieRepository->findByEtat();
        $now = new DateTime('now', new DateTimeZone('Europe/Paris'));
        foreach ($sorties as $sortie) {
            //Gestion de l'état inscription cloturée pour les sorties en etat "ouverte"
            if ($sortie->getEtat() == 2 && $sortie->getDateLimiteInscription() < $now) {
                $sortie->setEtat(3);
            }
            //Gestion de l'état "en cours" pour les sorties non annulées
            if ($sortie->getEtat() != 6 && $sortie->getDebutSortie() < $now && $sortie->getFinSortie() > $now) {
                $sortie->setEtat(4);
            }
            //Gestion de l'état sortie "passée" pour les sorties non annulées
            if ($sortie->getEtat() != 6 && $sortie->getFinSortie() < $now) {
                $sortie->setEtat(5);
            }
            //Gestion de l'état sortie "archivées"
            $delaiArchive = 5184000; //Délai audela duquel une sortie passée passe à l'état "archivée"
            $nowSecond = $now->getTimestamp();
            $dateFinSecond = $sortie->getFinSortie()->getTimestamp();
            if ($sortie->getEtat() != 6 && $dateFinSecond + $delaiArchive < $nowSecond) {
                $sortie->setEtat(7);
            }
            $this->entityManager->persist($sortie);
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}