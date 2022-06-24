<?php

namespace App\Repository;

use App\Entity\EventBookings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventBookings|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventBookings|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventBookings[]    findAll()
 * @method EventBookings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventBookingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventBookings::class);
    }

    // /**
    //  * @return EventBookings[] Returns an array of EventBookings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventBookings
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function save_new_event_booking($booking_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $user_id = ($booking_details['userid']);
        $record_id = ($booking_details['record_id']);
        $number_of_kids = $conn->quote($booking_details['number_of_kids']);
        $number_of_chaperone = $conn->quote($booking_details['number_of_chaperone']);
        $number_of_kids = $conn->quote($booking_details['number_of_kids']);
        $extra_info = $conn->quote($booking_details['extra_info']);

        $query = ('INSERT INTO `event_bookings`(`session_id`, `client_id`, `number_of_children`, `number_of_number_of_chaperone`, `extra_info`, `status`, `is_school_booking`) VALUES ('.$record_id.','.$user_id.','.$number_of_kids.','.$number_of_chaperone.','.$extra_info.',0,0);'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function get_my_sessions($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);


        $query = ('SELECT sessions_revamp.record_id AS sess_id,sessions_revamp.number_of_sessions,sessions_revamp.cost,event_bookings.record_id,event_bookings.booking_date,sessions_revamp.session_title,sessions_revamp.start_date,sessions_revamp.end_date,sessions_revamp.status 
        FROM `event_bookings` 
        JOIN sessions_revamp on event_bookings.session_id=sessions_revamp.record_id 
        WHERE event_bookings.`client_id`=' . $userid . ' AND event_bookings.`view_status`=1
        GROUP BY sessions_revamp.record_id
        ORDER BY event_bookings.record_id');

        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function check_pre_saved_event($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `event_bookings` WHERE view_status=1 AND `client_id`='.$user['userid'].' AND `session_id` = '.$user['record_id'].' ;'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_event_max_children($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT `max_attendee` FROM `sessions_revamp` WHERE `record_id` = '.$user['record_id'].' ;'); 
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_event_booking_so_far($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT SUM(`number_of_children`) AS number_of_children_so_far FROM `event_bookings` WHERE `session_id`= '.$user['record_id'].' ;'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
