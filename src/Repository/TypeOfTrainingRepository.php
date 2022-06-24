<?php

namespace App\Repository;

use App\Entity\TypeOfTraining;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeOfTraining|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOfTraining|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOfTraining[]    findAll()
 * @method TypeOfTraining[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOfTrainingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeOfTraining::class);
    }

    // /**
    //  * @return TypeOfTraining[] Returns an array of TypeOfTraining objects
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
    public function findOneBySomeField($value): ?TypeOfTraining
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
