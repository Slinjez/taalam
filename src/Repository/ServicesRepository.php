<?php

namespace App\Repository;

use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Services|null find($id, $lockMode = null, $lockVersion = null)
 * @method Services|null findOneBy(array $criteria, array $orderBy = null)
 * @method Services[]    findAll()
 * @method Services[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Services::class);
    }

    // /**
    //  * @return Services[] Returns an array of Services objects
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
    public function findOneBySomeField($value): ?Services
    {
    return $this->createQueryBuilder('s')
    ->andWhere('s.exampleField = :val')
    ->setParameter('val', $value)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    }
     */

    public function get_all_services()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `services` WHERE `status`=1;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_service_count(){
        $conn = $this->getEntityManager()
        ->getConnection();

        $query = ('SELECT COUNT(*) as service_count FROM `services`;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_trainer_services($trainer_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT services.record_id ,services.service_name

        FROM `trainer_activities`
        JOIN services ON trainer_activities.service_id=services.record_id

        WHERE `trainer_activities`.`trainer_id`=' . $trainer_id . ';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_trainer_services_morph($trainer_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT services.record_id ,services.service_name

        FROM `trainer_activities`
        JOIN services ON trainer_activities.service_id=services.record_id

        WHERE `trainer_activities`.`trainer_id`=' . $trainer_id . ';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_service_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `services` WHERE `status`=1;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function save_training_session($training_session_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $userid = $conn->quote($training_session_data['userid']);
        $trainer_id = $conn->quote($training_session_data['trainer_id']);
        $service = $conn->quote($training_session_data['service']);
        //$session_date = $training_session_data['session_date'];
        $session_date = $conn->quote($training_session_data['session_date']);
        $training_activities = $conn->quote($training_session_data['training_activities']);

        $query = ('INSERT INTO `training_sessions`(`session_id`, `client_id`, `trainer_id`, `session_date`,  `description` )
        VALUES
        (' . $service . ',' . $userid . ',' . $trainer_id . ',' . $session_date . ',' . $training_activities . ');');
        // echo($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_event($event_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $session_name = $conn->quote($event_details['session_name']);
        $tag_line = $conn->quote($event_details['tag_line']);
        $service_select = $conn->quote($event_details['service_select']);
        $session_date = $conn->quote($event_details['session_date']);
        $session_end_date = $conn->quote($event_details['session_end_date']);
        $session_location = $conn->quote($event_details['session_location']);
        $editor = $conn->quote($event_details['editor']);
        $cost = $conn->quote($event_details['cost']);
        $num_sessions = $conn->quote($event_details['num_sessions']);
        $is_published = ($event_details['is_published']);
        $thumbnail = $conn->quote('#');

        
        $num_chaperone_allowed = $conn->quote($event_details['num_chaperone_allowed']);
        $type_of_training = $conn->quote($event_details['type_of_training']);
        $age_bracket = $conn->quote($event_details['age_bracket']);
        $max_attendees = $conn->quote($event_details['max_attendees']);
        
        $query = ('INSERT INTO `sessions_revamp`(`session_title`, `tag_line`, `start_date`, `end_date`, `location`, `description`, `thumbnail`, `max_attendee`, `age_bracket`, `type_of_training`,`chaperone_allowed`,`number_of_sessions`,`cost`,`status`) VALUES ('.$session_name.','.$tag_line.','.$session_date.','.$session_end_date.','.$session_location.','.$editor.','.$thumbnail.','.$num_chaperone_allowed.','.$type_of_training.','.$age_bracket.','.$max_attendees.','.$num_sessions.','.$cost.','.$is_published.');');
        // echo($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function update_event($event_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $session_name = $conn->quote($event_details['session_name']);
        $tag_line = $conn->quote($event_details['tag_line']);
        $service_select = $conn->quote($event_details['service_select']);
        $session_date = $conn->quote($event_details['session_date']);
        $session_end_date = $conn->quote($event_details['session_end_date']);
        $session_location = $conn->quote($event_details['session_location']);
        $editor = $conn->quote($event_details['editor']);
        $thumbnail = $conn->quote('#');
        $record_id =$conn->quote($event_details['record_id']);
        $cost = $conn->quote($event_details['cost']);
        $num_sessions = $conn->quote($event_details['num_sessions']);
        $status_select = $conn->quote($event_details['status_select']);
        
        $num_chaperone_allowed = $conn->quote($event_details['num_chaperone_allowed']);
        $type_of_training = $conn->quote($event_details['type_of_training']);
        $age_bracket = $conn->quote($event_details['age_bracket']);
        $max_attendees = $conn->quote($event_details['max_attendees']);
        

        
        $query = ('UPDATE `sessions_revamp` SET `session_title`='.$session_name.',`tag_line`='.$tag_line.',`start_date`='.$session_date.',`end_date`='. $session_end_date.',`location`='.$session_location.',`description`='.$editor.',`status`='.$status_select.',`max_attendee`='.$max_attendees.',`chaperone_allowed`='.$num_chaperone_allowed.',`number_of_sessions`='.$num_sessions.',`cost`='.$cost.' WHERE `record_id`='.$record_id.';');
        // echo($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function set_thumbnail_location($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = $conn->quote($file_data['record_id']);
        $the_file_path = $conn->quote($file_data['file_path']);

        
        $query = ('UPDATE `sessions_revamp` SET `thumbnail`='.$the_file_path.' WHERE `record_id`='.$record_id.'');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function set_requirement_file($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = $conn->quote($file_data['record_id']);
        $the_file_path = $conn->quote($file_data['file_path']);

        
        $query = ('INSERT INTO `session_requirement_files`(`event_id`, `file_path`) VALUES ('.$record_id.','.$the_file_path.')');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_session_trainers($last_id,$selected_trainer)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('INSERT INTO `session_trainers`(`session_id`, `trainer_id`) VALUES ('.$last_id.','.$selected_trainer.')');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function delete_session_trainers($last_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('DELETE FROM `session_trainers` WHERE `session_id`='.$last_id.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function save_session_services($last_id,$service_select)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('INSERT INTO `sesssion_services`(`session_id`, `service_id`) VALUES ('.$last_id.','.$service_select.')');
        // \var_dump($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    
    
    public function delete_session_services($last_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('DELETE FROM sesssion_services WHERE `session_id`='.$last_id.'');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_session_trainings($last_id,$service_select)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('INSERT INTO `session_type_of_training`(`session_id`, `training_type`) VALUES ('.$last_id.','.$service_select.')');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function delete_session_trainings($last_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('DELETE FROM session_type_of_training WHERE session_id='.$last_id.'');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_session_ages($last_id,$service_select)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('INSERT INTO `session_age_bracket`(`session_id`, `age_bracket`) VALUES ('.$last_id.','.$service_select.')');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function delete_session_ages($last_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
                
        $query = ('DELETE FROM `session_age_bracket` WHERE `session_id`='.$last_id.'');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
}
