<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Stagiaire;
use App\Form\ImportStagiairesFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ImportStagiairesController extends AbstractController
{
    #[Route('/admin/import-stagiaire', name: '_import-stagiaires')]
    /**
     * Méthode pour importer des stagiaires en nombre depuis un fichier XLS/CSV.
     *
     * En 1ère ligne du fichier importé doivent apparaître les noms de colonnes suivants :
     * 1 | email | password | nom | prenom | telephone | campus |
     *
     * Cette méthode utilise une ligne de commande pour configurer la destination
     * des fichiers à importer :
     *
     * composer require phpoffice/phpspreadsheet
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function importStagiaires(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $form = $this->createForm(ImportStagiairesFormType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();

            if ($fichier) {
                // Chargement du fichier Excel avec PHPExcel/PHPSpreadsheet
                $tableur    = IOFactory::load($fichier);
                $classeur   = $tableur->getActiveSheet();
                $lignes     = $classeur->toArray(null, true, true, true);

                try {
                    // Démarrage d'une transaction
                    $entityManager->beginTransaction();

                    // Initialiation du compteur pour gestion de nombre de lignes insérées
                    $compteur = 0;

                    // On boucle sur chaque ligne à partir de la deuxième ligne, la première ligne correspondant aux titres des colonnes
                    foreach ($lignes as $cle => $ligne) {
                        if ($cle === 1) {
                            continue;
                        }
                        // Récupèration des données de la ligne courante
                        $data = [
                            'email'     => $ligne['A'],
                            'password'  => $ligne['B'],
                            'nom'       => $ligne['C'],
                            'prenom'    => $ligne['D'],
                            'telephone' => $ligne['E'],
                            'campus'    => $ligne['F']
                        ];
                        // Vérification de nullité
                        foreach ($data as $valeur) {
                            if ($valeur === null) {
                                $entityManager->rollback();
                                $this->addFlash('danger', 'Assurez-vous que toutes les données sont renseignées à la ligne ' . $cle . '.');
                                return $this->redirectToRoute('admin');
                            }
                        }
                        // Vérification et chargement de l'image par défaut
                        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/stagiaires_no_photo.png';
                        if (file_exists($imagePath)) {
                            $image = new UploadedFile($imagePath, 'stagiaires_no_photo.png', 'image/png', null, true);
                            $data['image'] = $image;
                        }
                        // Vérification de l'unicité de l'email
                        $email = $data['email'];
                        $stagiaireExistant = $entityManager->getRepository(Stagiaire::class)->findOneBy(['email' => $email]);
                        if ($stagiaireExistant) {
                            $entityManager->rollback();
                            $this->addFlash('danger', sprintf('L\'email %s à la ligne %d existe déjà en base de données.', $email, $cle));
                            return $this->redirectToRoute('admin');
                        }
                        // Création d'un objet Stagiaire et soumission des données au formulaire d'importation
                        $campus = $entityManager->getReference(Campus::class, $data['campus']);
                        $stagiaire = (new Stagiaire())
                            ->setCampus($campus)
                            ->setEmail($data['email'])
                            ->setRoles(['ROLE_USER'])
                            ->setNom($data['nom'])
                            ->setPrenom($data['prenom'])
                            ->setTelephone($data['telephone'])
                            ->setAdministrateur(false)
                            ->setActif(true)
                            ->setPremiereConnexion(false)
                            ->setUpdatedAt(new DateTime('now'));
                        // Affectation de l'image par défaut au stagiaire
                        $stagiaire->setImage($data['image']);
                        // Hashage du mot de passe avec la méthode hash de Security Symfony
                        $hashedPassword = $passwordHasher->hashPassword($stagiaire, $data['password']);
                        $stagiaire->setPassword($hashedPassword);
                        // Persistance de l'objet Stagiaire en base de données
                        try {
                            $entityManager->persist($stagiaire);
                            $entityManager->flush();
                        } catch (Exception) {
                            // En cas d'erreur, annulation de l'opération
                            $entityManager->rollback();
                            $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement. Assurez-vous que les données renseignées respectent le format attendu et réessayez.');
                            return $this->redirectToRoute('admin');
                        }

                        $compteur++;

                    }

                    // Enregistrement des modifications dans la base de données
                    $entityManager->commit();
                    $this->addFlash('success', sprintf('Le fichier a été importé avec succès. %d lignes ont été insérées en base de données.', $compteur));
                    return $this->redirectToRoute('admin');

                } catch (Exception) {
                    // En cas d'erreur, annulation de l'opération
                    $entityManager->rollback();
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement. Assurez-vous que les données renseignées respectent le format attendu et réessayez.');

                } finally {
                    // Libération des ressources
                    $entityManager->close();
                }

            }

        }

        return $this->render('import_stagiaires/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}


