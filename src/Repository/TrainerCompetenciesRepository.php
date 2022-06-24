<?php

namespace App\Repository;

use App\Entity\TrainerCompetencies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainerCompetencies|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainerCompetencies|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainerCompetencies[]    findAll()
 * @method TrainerCompetencies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainerCompetenciesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainerCompetencies::class);
    }

    // /**
    //  * @return TrainerCompetencies[] Returns an array of TrainerCompetencies objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrainerCompetencies
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
