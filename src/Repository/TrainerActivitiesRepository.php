<?php

namespace App\Repository;

use App\Entity\TrainerActivities;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainerActivities|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainerActivities|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainerActivities[]    findAll()
 * @method TrainerActivities[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainerActivitiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainerActivities::class);
    }

    // /**
    //  * @return TrainerActivities[] Returns an array of TrainerActivities objects
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
    public function findOneBySomeField($value): ?TrainerActivities
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
