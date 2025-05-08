<?php

namespace App\Repository;

use App\Entity\Analyse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Analyse>
 */
class AnalyseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analyse::class);
    }

    //    /**
    //     * @return Analyse[] Returns an array of Analyse objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Analyse
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // src/Repository/AnalyseRepository.php

    public function searchByTerm(string $term): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.RendezVous', 'r')
            ->where('a.date LIKE :term')
            ->orWhere('a.type LIKE :term')
            ->orWhere('r.id = :id')
            ->setParameter('term', '%'.$term.'%')
            ->setParameter('id', is_numeric($term) ? (int)$term : 0)
            ->getQuery()
            ->getResult();
    }
}
