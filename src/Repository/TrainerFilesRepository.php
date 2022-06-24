<?php

namespace App\Repository;

use App\Entity\TrainerFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainerFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainerFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainerFiles[]    findAll()
 * @method TrainerFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainerFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainerFiles::class);
    }

    // /**
    //  * @return TrainerFiles[] Returns an array of TrainerFiles objects
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
    public function findOneBySomeField($value): ?TrainerFiles
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function get_trainer_files($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `trainer_files` 
        WHERE client_id='.$user_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function  get_trainer_file_by_id($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `trainer_files` 
        WHERE record_id='.$record_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
