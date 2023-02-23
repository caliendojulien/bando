<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Stagiaire;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMinSortieDate(): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT MIN(s.debutSortie) as minDate FROM App\Entity\Sortie s'
            )
            ->getResult();
    }

    public function findMaxSortieDate(): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT MAX(s.finSortie) as maxDate FROM App\Entity\Sortie s'
            )
            ->getResult();
    }

    public function findSorties(
        ?string   $nom = null,
        ?DateTime $debutSortie = null,
        ?DateTime $finSortie = null,
        ?Campus   $campus = null,
        ?bool     $organisateur = false,
        Stagiaire $user = null,
        ?bool     $inscrit = false,
        ?bool     $sorties_ouvertes = false
    ): array
    {
        $query_builder = $this->createQueryBuilder('s')
            ->leftJoin('s.campus', 'c')
            ->leftJoin('s.participants', 'p');

        // Gestion des input

        if ($nom) {
            $query_builder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%');
        }

        if ($debutSortie) {
            $query_builder->andWhere('s.debutSortie >= :debutSortie')
                ->setParameter('debutSortie', $debutSortie);
        }

        if ($finSortie) {
            $query_builder->andWhere('s.debutSortie <= :finSortie')
                ->setParameter('finSortie', $finSortie);
        }

        if ($campus) {
            $query_builder->andWhere('c = :campus')
                ->setParameter('campus', $campus);
        }

        // Gestion des cases à cocher
        switch (true) {
            case ($organisateur && $inscrit && $sorties_ouvertes):
                $query_builder->andWhere('s.organisateur = :user OR s.organisateur != :user OR s.etat = :etat')
                    ->setParameter('user', $user)
                    ->setParameter('etat', 2);
                break;
            case ($organisateur && $inscrit):
                $query_builder->andWhere('s.organisateur = :user OR :user MEMBER OF s.participants ')
                    ->setParameter('user', $user);
                break;
            case ($organisateur):
                $query_builder->andWhere('s.organisateur = :user')
                    ->setParameter('user', $user);
                break;
            case ($inscrit):
                $query_builder->andWhere(':user MEMBER OF s.participants')
                    ->setParameter('user', $user);
                break;
            case ($organisateur && $sorties_ouvertes):
                $query_builder->andWhere('s.organisateur = :user OR s.etat <= :etat')
                    ->setParameter('user', $user)
                    ->setParameter('etat', 2);
                break;
            case($sorties_ouvertes):
                $query_builder->andWhere('s.etat <= :etat')
                    ->setParameter('etat', 2);
                break;
            default:
                // Traiter les cas où toutes les cases à cocher sont décochées en retournant toutes les sorties
                break;
        }

        return
            array_filter(
                $query_builder->getQuery()->getResult(),
                function ($sortie) use ($user) {
                    return
                        $sortie->getEtat() != 1 || ($sortie->getEtat() === 1 && $sortie->getOrganisateur() === $user);

                });
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByEtat(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat != 7')
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
