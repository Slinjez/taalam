<?php

namespace App\Repository;

use App\Entity\AgeBrackets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AgeBrackets|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgeBrackets|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgeBrackets[]    findAll()
 * @method AgeBrackets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgeBracketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgeBrackets::class);
    }

    // /**
    //  * @return AgeBrackets[] Returns an array of AgeBrackets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AgeBrackets
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
