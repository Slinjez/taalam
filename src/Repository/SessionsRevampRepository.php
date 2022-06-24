<?php

namespace App\Repository;

use App\Entity\SessionsRevamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SessionsRevamp|null find($id, $lockMode = null, $lockVersion = null)
 * @method SessionsRevamp|null findOneBy(array $criteria, array $orderBy = null)
 * @method SessionsRevamp[]    findAll()
 * @method SessionsRevamp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionsRevampRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionsRevamp::class);
    }

    // /**
    //  * @return SessionsRevamp[] Returns an array of SessionsRevamp objects
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
    public function findOneBySomeField($value): ?SessionsRevamp
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function get_all_training_list($filter_record_id=0)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        if($filter_record_id==0){
            $query = ('SELECT `record_id`,`session_title`,`tag_line`,`start_date`,`end_date`,`location`,`status`,`thumbnail`,`max_attendee`,`age_bracket`,`type_of_training`,`chaperone_allowed` FROM `sessions_revamp` WHERE `status`=1 GROUP BY sessions_revamp.`record_id` ORDER BY record_id DESC;');
        }else{
            $query = ('SELECT sessions_revamp.`record_id`,`session_title`,`tag_line`,`start_date`,`end_date`,`location`,`status`,`thumbnail`,`max_attendee`,`age_bracket`,`type_of_training`,`chaperone_allowed` 
            FROM `sessions_revamp` 
            JOIN sesssion_services on sessions_revamp.record_id=sesssion_services.session_id
            WHERE `status`=1 AND sesssion_services.service_id='.$filter_record_id.' GROUP BY sessions_revamp.`record_id` ORDER BY record_id DESC;');

        }
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_view_event_training($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `number_of_sessions`,`cost`,`record_id`,`session_title`,`tag_line`, `description`,`start_date`,`end_date`,`location`,`status`,`thumbnail`,`max_attendee`,`age_bracket`,`type_of_training`,`chaperone_allowed` FROM `sessions_revamp` WHERE `record_id` = '.$record_id.';');
        // echo  $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_event_training($identify)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT session_type_of_training.`training_type`, type_of_training.description FROM `session_type_of_training` JOIN type_of_training on session_type_of_training.training_type=type_of_training.record_id WHERE session_type_of_training.session_id = '.$identify.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_event_age_bracket($identify)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT session_age_bracket.age_bracket,age_brackets.description
        FROM `session_age_bracket` 
        JOIN age_brackets on session_age_bracket.age_bracket =age_brackets.record_id
        WHERE session_age_bracket.session_id= '.$identify.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    

    public function get_all_event_requirement_pdf($identify)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `session_requirement_files` WHERE `event_id`= '.$identify.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_revamp_upcoming_and_ongoing_sessions()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT session_age_bracket.age_bracket,age_brackets.description
        FROM `session_age_bracket` 
        JOIN age_brackets on session_age_bracket.age_bracket =age_brackets.record_id
        WHERE session_age_bracket.session_id= '.$identify.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_revamp_events_adm_ovr()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT sessions_revamp.`record_id`,sessions_revamp.`session_title`,sessions_revamp.`start_date`,sessions_revamp.`end_date`,sessions_revamp.`location`,sessions_revamp.`status`,sessions_revamp.`thumbnail`
        FROM `sessions_revamp` WHERE sessions_revamp.`status` IN (1) ORDER BY  sessions_revamp.`record_id` DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_revamp_events_adm_ovr_ongoing()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT sessions_revamp.`record_id`,sessions_revamp.`session_title`,sessions_revamp.`start_date`,sessions_revamp.`end_date`,sessions_revamp.`location`,sessions_revamp.`status`,sessions_revamp.`thumbnail`
        FROM `sessions_revamp` WHERE sessions_revamp.`status` IN (2) ORDER BY  sessions_revamp.`record_id` DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_view_event_booking($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `event_bookings` WHERE record_id = '.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_view_event_kids_booking($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `session_kids`.`child_id`, `member_kids`.`kidsname` , `member_kids`.`date_of_birth` 
        FROM `session_kids` 
        JOIN member_kids on session_kids.child_id=member_kids.record_id
        WHERE `session_kids`.`session_id`= '.$record_id.';');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_kids_session_count($record_id,$child_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS session_count FROM `session_kids_register` WHERE `session_id`='.$record_id.' AND `child_id`='.$child_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_view_event_trainer_list($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `session_trainers`.`record_id`,`session_trainers`.`trainer_id`,clients.user_name,clients.traiver_verification_status  

        FROM `session_trainers` 
        JOIN trainer_profiles on session_trainers.trainer_id=trainer_profiles.record_id
        JOIN clients on trainer_profiles.client_look_up_id = clients.record_id
        
        WHERE `session_id`='.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
