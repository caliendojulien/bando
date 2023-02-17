<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Stagiaire;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker;
use PHPUnit\Framework\Warning;

class AppFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        //Initialisation de Faker sur un environnement français
        $faker = Faker\Factory::create('fr_FR');

        //Création de 20 campus.
        $campus = array();
        for ($i = 0; $i < 20; $i++) {
            $campus[$i] = new Campus();
            $campus[$i]->setNom("ENI-" . $faker->city);
            $manager->persist($campus[$i]);
        }
        //Création de 30 villes
        $villes = array();
        for ($i = 0; $i < 30; $i++) {
            $villes[$i] = new Ville();
            $villes[$i]->setNom($faker->city);
            $villes[$i]->setCodePostal(rand(10000, 95900));
            $manager->persist($villes[$i]);
        }

        //Création de 30 lieux reprenant une ville au hazard dans les villes crées
        $lieux = array();
        for ($i = 0; $i < 30; $i++) {
            $ramdom_key_ville = array_rand($villes, 1);
            $lieux[$i] = new Lieu();
            $lieux[$i]->setVille($villes[$ramdom_key_ville]);
            $lieux[$i]->setNom($faker->company);
            $lieux[$i]->setRue($faker->address);
            $lieux[$i]->setLatitude($faker->latitude);
            $lieux[$i]->setLongitude($faker->longitude);
            $manager->persist($lieux[$i]);
        }

        //Création de 100 stagiaires. Les elements boolean (Actif,Administrateur, PremiereConnexion) sont choisi au hazard. Le campus est également affectés au hazard parmis les campus crées.
        $stagiaires = array();
        for ($i = 0; $i < 100; $i++) {
            $ramdom_key_campus = array_rand($campus, 1);
            $stagiaires[$i] = new Stagiaire();
            $stagiaires[$i]->setCampus($campus[$ramdom_key_campus]);
            $stagiaires[$i]->setNom($faker->name);
            $stagiaires[$i]->setPrenom($faker->firstName);
            $stagiaires[$i]->setEmail($faker->email);
            $stagiaires[$i]->setTelephone("0658787562");
            $stagiaires[$i]->setPassword('$2y$13$mt1o5tcLVkc2PR/hC.W7JuwF0BA1bS9U/kMfIRdC7DvWV/xomhbJq');
            $stagiaires[$i]->setUrlPhoto('images/stagiaires_no_photo.png');
            $stagiaires[$i]->setAdministrateur(rand(0, 1));
            $stagiaires[$i]->setRoles($stagiaires[$i]->isAdministrateur() ? ["ROLE_ADMIN", "ROLE_USER"] : ["ROLE_USER"]);
            $stagiaires[$i]->setActif(rand(0, 1));
            $stagiaires[$i]->setPremiereConnexion(rand(0, 1));
            $manager->persist($stagiaires[$i]);
        }

        //Création de 500 sorties
        $sorties = array();
        for ($i = 0; $i < 500; $i++) {
            //Définition d'une date de début de sortie random
            $date_ref = new DateTime();
            $second_ref = $date_ref->getTimestamp();
            $rand_val_debut = rand(-50000000, 10000000);
            $rand_debut = $second_ref + $rand_val_debut;
            $rand_date_debut = new DateTime();
            $rand_date_debut->setTimestamp($rand_debut);

            //Définition d'une date de fin inscription random
            $rand_val_inscription_max = rand(87000, 260000);
            $rand_val_inscription = $rand_debut - $rand_val_inscription_max;
            $rand_date_max_inscription = new DateTime();
            $rand_date_max_inscription->setTimestamp($rand_val_inscription);

            //Définition d'une date de fin sortie random
            $rand_val_fin = rand(18000, 10000000);
            $val_fin = $rand_val_inscription + $rand_val_fin;
            $rand_date_fin = new DateTime();
            $rand_date_fin->setTimestamp($val_fin);

            $ramdom_key_lieu = array_rand($lieux, 1);
            $ramdom_key_campus = array_rand($campus, 1);
            $random_keys_stagiaire = array_rand($stagiaires, 1);
            $sorties[$i] = new Sortie();
            $sorties[$i]->setLieu($lieux[$ramdom_key_lieu]);
            $sorties[$i]->setCampus($campus[$ramdom_key_campus]);
            $sorties[$i]->setOrganisateur($stagiaires[$random_keys_stagiaire]);
            $sorties[$i]->setNom("Ma sortie à " . $faker->city . ' - ' . $faker->company);

            $sorties[$i]->setDebutSortie($rand_date_debut);
            $sorties[$i]->setFinSortie($rand_date_fin);
            $sorties[$i]->setDateLimiteInscription($rand_date_max_inscription);

            $sorties[$i]->setNombreInscriptionsMax(rand(1, 200));
            $sorties[$i]->setInfosSortie("Les informations sur la sorties seront à renseigner dans cette espace.Les informations sur la sorties seront à renseigner dans cette espace.");

            $now = new DateTime('now');
            if ($rand_date_fin < $now) {
                $sorties[$i]->setEtat(rand(5, 7));
            }
            if ($rand_date_max_inscription < $now && $rand_date_debut > $now) {
                $sorties[$i]->setEtat(3);
            }
            if ($rand_date_debut > $now && $sorties[$i]->getEtat() != 3) {
                $sorties[$i]->setEtat(rand(1, 2));
            }
            if ($rand_date_debut < $now && $rand_date_fin > $now) {
                $sorties[$i]->setEtat(4);
            }

            //ajout de participants à la sorties
            $rand_nb_participants = rand(2, 30);
            $random_keys_stagiaire = array_rand($stagiaires, $rand_nb_participants);

            if ($sorties[$i]->getEtat() >= 2) {
                foreach ($random_keys_stagiaire as $key) {
                    $sorties[$i]->addParticipant($stagiaires[$key]);
                }
            }
            if ($sorties[$i]->getEtat() === 6) {
                $sorties[$i]->setMotifAnnulation("Voici pourquoi j'ain annulé");
            }
            $manager->persist($sorties[$i]);

        }
        $manager->flush();
    }
}