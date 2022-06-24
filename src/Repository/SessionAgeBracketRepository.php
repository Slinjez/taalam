<?php

namespace App\Repository;

use App\Entity\SessionAgeBracket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionAgeBracket|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionAgeBracket|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionAgeBracket[]    findAll()
 * @method SessionAgeBracket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionAgeBracketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionAgeBracket::class);
    }

    // /**
    //  * @return SessionAgeBracket[] Returns an array of SessionAgeBracket objects
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
    public function findOneBySomeField($value): ?SessionAgeBracket
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
