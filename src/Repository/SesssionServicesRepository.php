<?php

namespace App\Repository;

use App\Entity\SesssionServices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SesssionServices|null find($id, $lockMode = null, $lockVersion = null)
 * @method SesssionServices|null findOneBy(array $criteria, array $orderBy = null)
 * @method SesssionServices[]    findAll()
 * @method SesssionServices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SesssionServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SesssionServices::class);
    }

    // /**
    //  * @return SesssionServices[] Returns an array of SesssionServices objects
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
    public function findOneBySomeField($value): ?SesssionServices
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
