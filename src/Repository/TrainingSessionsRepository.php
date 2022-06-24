<?php

namespace App\Repository;

use App\Entity\TrainingSessions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingSessions|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingSessions|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingSessions[]    findAll()
 * @method TrainingSessions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingSessionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingSessions::class);
    }

    // /**
    //  * @return TrainingSessions[] Returns an array of TrainingSessions objects
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
    public function findOneBySomeField($value): ?TrainingSessions
    {
    return $this->createQueryBuilder('t')
    ->andWhere('t.exampleField = :val')
    ->setParameter('val', $value)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    }
     */

    public function get_my_sessions($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE `training_sessions`.`client_id`=' . $userid . '

        GROUP BY training_sessions.record_id;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_session_count($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS session_count
        FROM `event_bookings`
        WHERE `event_bookings`.`client_id`=' . $userid . ' AND view_status=1;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_session_data($data_t_save)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('UPDATE `event_bookings` SET view_status=' . $data_t_save['click_act'] . ' WHERE `session_id`=' . $data_t_save['record_id'] . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        //$results = $sth->fetchAll();

        return true;
    }

    public function get_my_sessions_short($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE `training_sessions`.`client_id`=' . $userid . '

        GROUP BY training_sessions.record_id

        LIMIT 5;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_new_sessions($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`session_date`,`training_sessions`.`status`,`training_sessions`.`rating`

        FROM `training_sessions`
        JOIN training_sessions on training_sessions.session_id = training_sessions.session_id
        JOIN clients on clients.record_id = training_sessions.record_id

        WHERE `status`=1;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_my_sessions_adm_x()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE `training_sessions`.`status`=1 OR  `training_sessions`.`status`=2

        GROUP BY training_sessions.record_id;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_my_sessions_adm()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `sessions_revamp` ORDER BY record_id DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_ongoing_sessions_adm()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE  `training_sessions`.`status`=3

        GROUP BY training_sessions.record_id;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_canceled_sessions_adm()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE  `training_sessions`.`status`=5

        GROUP BY training_sessions.record_id;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_complete_sessions_adm()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE  `training_sessions`.`status`=4

        GROUP BY training_sessions.record_id;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function trainer_get_my_sessions_old($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `training_sessions`.`record_id`,`training_sessions`.`session_booked_date`,`training_sessions`.`trainer_id`,`training_sessions`.`session_date`,`training_sessions`.`title`,`training_sessions`.`description`,`training_sessions`.`status`,`training_sessions`.`rating`,clients.user_name, services.service_name

        FROM `training_sessions`
        JOIN clients ON clients.record_id=training_sessions.client_id
        LEFT JOIN trainer_profiles ON training_sessions.trainer_id=trainer_profiles.record_id
        LEFT JOIN services on training_sessions.session_id = training_sessions.session_id

        WHERE  trainer_profiles.client_look_up_id=' . $userid . '

        GROUP BY training_sessions.record_id;');

        $query = ('SELECT sessions_revamp.record_id,sessions_revamp.session_title,sessions_revamp.start_date,sessions_revamp.end_date,sessions_revamp.location,sessions_revamp.status

        FROM `session_trainers`
        JOIN sessions_revamp on session_trainers.session_id = session_trainers.session_id


        WHERE `session_trainers`.`trainer_id`=' . $userid . '
        GROUP BY sessions_revamp.record_id
        ORDER BY sessions_revamp.record_id DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function trainer_get_my_sessions($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        // echo $userid;
        // exit;
        $query = ('SELECT `sessions_revamp`.`record_id`,`sessions_revamp`.`session_title`,`sessions_revamp`.`start_date`,`sessions_revamp`.`end_date`,`sessions_revamp`.`location`,`sessions_revamp`.`status`,`sessions_revamp`.`number_of_sessions`,`sessions_revamp`.`cost`

        FROM `sessions_revamp`

        JOIN session_trainers on session_trainers.session_id=sessions_revamp.record_id
        JOIN trainer_profiles on session_trainers.trainer_id = trainer_profiles.record_id

        WHERE trainer_profiles.record_id=' . $userid . '
        GROUP BY `sessions_revamp`.`record_id`

        ORDER BY `sessions_revamp`.`record_id` DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_full_session_count()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS session_count
        FROM `sessions_revamp`;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_full_event_book_count()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS session_count
        FROM `event_bookings` WHERE view_status=1;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_graph_complete_sessions()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS training_sessions FROM `training_sessions` WHERE (`session_date` > (NOW() - INTERVAL 30 DAY)) AND `status`=4;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();
        // var_dump($results);
        // exit;
        return $results;
    }

    public function get_graph_pending_sessions()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS training_sessions FROM `training_sessions` WHERE (`session_date` > (NOW() - INTERVAL 30 DAY)) AND `status`<>4;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_graph_day_activity_session_count($day, $is_confirmed)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$day = $conn->quote($day);

        if ($is_confirmed) {
            $query = ('SELECT COUNT(*) AS training_sessions FROM `training_sessions` WHERE (DATE(`session_date`) = ' . $day . ') AND `status`=4;');
        } else {
            $query = ('SELECT COUNT(*) AS training_sessions FROM `training_sessions` WHERE (DATE(`session_date`) = ' . $day . ') AND `status`<>4;');
        }
        //echo $query;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_revamp_events_adm()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT sessions_revamp.`record_id`,sessions_revamp.`session_title`,sessions_revamp.`start_date`,sessions_revamp.`end_date`,sessions_revamp.`location`,sessions_revamp.`status`,sessions_revamp.`thumbnail`
        FROM `sessions_revamp` ORDER BY sessions_revamp.`record_id` DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_revamp_events_adm_complete()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT sessions_revamp.`record_id`,sessions_revamp.`session_title`,sessions_revamp.`start_date`,sessions_revamp.`end_date`,sessions_revamp.`location`,sessions_revamp.`status`,sessions_revamp.`thumbnail`
        FROM `sessions_revamp` WHERE sessions_revamp.`status`=3 ORDER BY sessions_revamp.`record_id` DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_events_adm($data_t_save)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($data_t_save['record_id']);
        $click_act = ($data_t_save['click_act']);

        $query = ('UPDATE `sessions_revamp` SET `status`=' . $click_act . ' WHERE `record_id`=' . $record_id . '');

        $sth = $conn->prepare($query);
        $sth->execute();
        //$rowsAffected = $conn->lastInsertId();

        return true;
    }

    public function get_session_trainers($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        // echo $userid;
        // exit;
        $query = ('SELECT `session_trainers`.`trainer_id`, clients.user_name

        FROM `session_trainers`

        JOIN trainer_profiles on session_trainers.trainer_id =trainer_profiles.record_id
        JOIN clients on trainer_profiles.client_look_up_id=clients.record_id

        WHERE `session_trainers`.`session_id`=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_session_event_type($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        // echo $userid;
        // exit;
        $query = ('SELECT services.service_name
        FROM `sesssion_services`
        JOIN services on sesssion_services.service_id=services.record_id
        WHERE `sesssion_services`.`session_id`=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_revamp_events_adm_by_id($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(`record_id`) AS session_count FROM `event_bookings` WHERE `client_id`=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function event_summary($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `event_bookings`.`record_id`,clients.user_name,`event_bookings`.`client_id`,`event_bookings`.`booking_date`,`event_bookings`.`number_of_children`
        FROM `event_bookings`
        JOIN clients ON event_bookings.client_id=clients.record_id
        WHERE event_bookings.session_id=1;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_revamp_events_child_list($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT member_kids.kidsname,member_kids.record_id,member_kids.date_of_birth,member_kids.allergies,member_kids.medical_conditions,member_kids.status,clients.user_name as parent_name,clients.email_address,clients.phone
        FROM `session_kids` 
        JOIN member_kids on session_kids.child_id=member_kids.record_id
        JOIN clients on member_kids.member_id = clients.record_id
        WHERE `session_kids`.`session_id`=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }


    public function get_kid_presense($record_id,$kids_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `session_kids_register` WHERE `session_id`='.$record_id.' AND `child_id`='.$kids_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
