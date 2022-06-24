<?php

namespace App\Repository;

use App\Entity\SessionTrainers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionTrainers|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionTrainers|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionTrainers[]    findAll()
 * @method SessionTrainers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionTrainersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionTrainers::class);
    }

    // /**
    //  * @return SessionTrainers[] Returns an array of SessionTrainers objects
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
    public function findOneBySomeField($value): ?SessionTrainers
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
