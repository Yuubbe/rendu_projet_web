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
     *
     * @param int|null $genreId Identifiant d'un genre à filtrer
     * @param int|null $annee   Année de production
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

    /**
     * Recherche avancée : mot-clé + filtres combinés (genres multiples, fourchette d'année).
     * Retourne un tableau associatif minimal pour l'autocomplétion/JS.
     *
     * @param string|null $term      Mot-clé (titre/synopsis)
     * @param int[]       $genreIds  Liste d'identifiants de genres
     * @param int|null    $anneeMin  Année minimale
     * @param int|null    $anneeMax  Année maximale
     * @param int         $limit     Nombre max de résultats
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchAdvanced(?string $term, array $genreIds = [], ?int $anneeMin = null, ?int $anneeMax = null, int $limit = 15): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id', 'f.titre', 'f.annee', 'f.prixLocationDefaut', 'f.affiche')
            ->leftJoin('f.genres', 'g');

        if ($term) {
            $qb->andWhere('LOWER(f.titre) LIKE :term OR LOWER(f.synopsis) LIKE :term')
               ->setParameter('term', '%'.mb_strtolower($term).'%');
        }

        if (!empty($genreIds)) {
            $qb->andWhere('g.id IN (:gids)')->setParameter('gids', $genreIds);
        }

        if ($anneeMin) {
            $qb->andWhere('f.annee >= :amin')->setParameter('amin', $anneeMin);
        }

        if ($anneeMax) {
            $qb->andWhere('f.annee <= :amax')->setParameter('amax', $anneeMax);
        }

        $qb->orderBy('f.titre', 'ASC')
           ->setMaxResults($limit);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Suggestions simples basées sur préfixe (auto-complétion) ou fallback sans terme.
     *
     * @param string|null $term  Préfixe de recherche
     * @param int         $limit Nombre max de suggestions
     *
     * @return string[]
     */
    public function suggestTitles(?string $term, int $limit = 5): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.id', 'f.titre')
            ->orderBy('f.titre', 'ASC')
            ->setMaxResults($limit);

        if ($term) {
            $qb->andWhere('LOWER(f.titre) LIKE :pref')
               ->setParameter('pref', mb_strtolower($term).'%');
        }

        return array_map(static fn ($row) => $row['titre'], $qb->getQuery()->getArrayResult());
    }

    /**
     * Renvoie des films par affinité de genres, en excluant certains IDs.
     *
     * @param int[] $genreIds   Genres privilégiés
     * @param int[] $excludeIds Films à exclure
     * @param int   $limit      Nombre max
     *
     * @return Film[]
     */
    public function findByGenresPrioritized(array $genreIds, array $excludeIds = [], int $limit = 10): array
    {
        if (empty($genreIds)) {
            return [];
        }

        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.genres', 'g')
            ->addSelect('COUNT(g.id) AS HIDDEN genreScore')
            ->where('g.id IN (:gids)')
            ->setParameter('gids', $genreIds)
            ->groupBy('f.id')
            ->orderBy('genreScore', 'DESC')
            ->addOrderBy('f.titre', 'ASC')
            ->setMaxResults($limit);

        if (!empty($excludeIds)) {
            $qb->andWhere('f.id NOT IN (:exclude)')->setParameter('exclude', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retourne les films les plus populaires (favoris), avec exclusions.
     *
     * @param int   $limit      Nombre max
     * @param int[] $excludeIds Films à exclure
     *
     * @return Film[]
     */
    public function findMostPopular(int $limit = 10, array $excludeIds = []): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.utilisateursFavoris', 'uf')
            ->addSelect('COUNT(uf.id) AS HIDDEN favCount')
            ->groupBy('f.id')
            ->orderBy('favCount', 'DESC')
            ->addOrderBy('f.titre', 'ASC')
            ->setMaxResults($limit);

        if (!empty($excludeIds)) {
            $qb->andWhere('f.id NOT IN (:exclude)')->setParameter('exclude', $excludeIds);
        }

        return $qb->getQuery()->getResult();
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
