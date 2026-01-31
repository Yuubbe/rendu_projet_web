<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    /**
     * Retourne un QueryBuilder filtré par genre et/ou année.
     */
    public function createFilteredQueryBuilder(?int $genreId, ?int $annee)
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.genres', 'g')
            ->addSelect('g');

        if ($genreId) {
            $qb->andWhere('g.id = :genreId')
               ->setParameter('genreId', $genreId);
        }

        if ($annee) {
            $qb->andWhere('f.annee = :annee')
               ->setParameter('annee', $annee);
        }

        return $qb->orderBy('f.titre', 'ASC');
    }

    //    /**
    //     * @return Film[] Returns an array of Film objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Film
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
