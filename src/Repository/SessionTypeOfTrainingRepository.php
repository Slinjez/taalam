<?php

namespace App\Repository;

use App\Entity\SessionTypeOfTraining;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionTypeOfTraining|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionTypeOfTraining|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionTypeOfTraining[]    findAll()
 * @method SessionTypeOfTraining[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionTypeOfTrainingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionTypeOfTraining::class);
    }

    // /**
    //  * @return SessionTypeOfTraining[] Returns an array of SessionTypeOfTraining objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SessionTypeOfTraining
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
