<?php

namespace App\Repository;

use App\Entity\TrainerProfiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainerProfiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainerProfiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainerProfiles[]    findAll()
 * @method TrainerProfiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainerProfilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainerProfiles::class);
    }

    // /**
    //  * @return TrainerProfiles[] Returns an array of TrainerProfiles objects
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
    public function findOneBySomeField($value): ?TrainerProfiles
    {
    return $this->createQueryBuilder('t')
    ->andWhere('t.exampleField = :val')
    ->setParameter('val', $value)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    }
     */

    public function get_trainer_by_id($trainer_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `trainer_profiles` WHERE `trainer_profiles`.record_id = ' . $trainer_id . ';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_trainers()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT clients.`user_name`, clients.`profile_picture`, clients.`record_id`,trainer_profiles.`record_id` AS trainer_id, trainer_profiles.gender FROM `clients` JOIN trainer_profiles on clients.record_id = trainer_profiles.client_look_up_id WHERE clients.is_trainer=1;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_trainer_by_user_id($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `trainer_profiles` WHERE client_look_up_id=' . $user_id . ';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_trainer_count()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT COUNT(*) AS trainer_count FROM `trainer_profiles`;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_this_trainer($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT clients.`user_name`, clients.`profile_picture`, clients.`record_id`, trainer_profiles.gender, trainer_profiles.record_id AS trainer_profile_id FROM `clients` JOIN trainer_profiles on clients.record_id = trainer_profiles.client_look_up_id WHERE trainer_profiles.record_id=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_trainer_activities($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT trainer_activities.record_id,trainer_activities.trainer_id,trainer_activities.service_id, services.service_name

        FROM `trainer_activities`

        JOIN services on services.record_id = trainer_activities.service_id

        WHERE trainer_activities.trainer_id=' . $record_id . ';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_top_trainers()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT clients.`user_name`, clients.`profile_picture`, trainer_profiles.`record_id`, trainer_profiles.gender

        FROM `clients`

        JOIN trainer_profiles on clients.record_id = trainer_profiles.client_look_up_id

        WHERE trainer_profiles.is_top=1
        ORDER BY trainer_profiles.top_status_date ASC
        LIMIT 5;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function save_trainer_profile($user_info_with_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $education_qualification = $conn->quote($user_info_with_id['education_qualification']);
        $client_bio = $conn->quote($user_info_with_id['client_bio']);
        $client_id = $conn->quote($user_info_with_id['client_id']);
        $InputAddress = $conn->quote($user_info_with_id['InputAddress']);

        $query = ('INSERT INTO `client_profiles`(`client_id`, `location`, `bio`, `education_qualification`) VALUES ('.$client_id.','.$InputAddress.','.$client_bio.','.$education_qualification.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function save_trainer_profile_tr($user_info_with_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $education_qualification = $conn->quote($user_info_with_id['education_qualification']);
        $client_bio = $conn->quote($user_info_with_id['client_bio']);
        $client_id = $conn->quote($user_info_with_id['client_id']);
        $InputAddress = $conn->quote($user_info_with_id['InputAddress']);

        $query = ('INSERT INTO `trainer_profiles`(`client_look_up_id`, `status`, `gender`, `is_top`) VALUES ('.$client_id.',0," ",0);'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    
    public function save_trainer_profile_competencies($last_id,$competency)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $competency = $conn->quote($competency);
        // $client_bio = $conn->quote($user_info_with_id['client_bio']);
        // $client_id = $conn->quote($user_info_with_id['client_id']);
        // $InputAddress = $conn->quote($user_info_with_id['InputAddress']);

        $query = ('INSERT INTO `trainer_competencies`(`trainer_id`, `competency`) VALUES ('.$last_id.','.$competency.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function set_upload_location($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = $conn->quote($file_data['record_id']);
        $the_file_path = $conn->quote($file_data['file_path']);

        $query = ('INSERT INTO `trainer_files`(`client_id`, `file_path`) VALUES ('.$record_id.','.$the_file_path.');'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function get_all_trainer_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT clients.`user_name`, clients.`profile_picture`, trainer_profiles.`record_id`, trainer_profiles.gender

        FROM `clients`

        JOIN trainer_profiles on clients.record_id = trainer_profiles.client_look_up_id

        ORDER BY trainer_profiles.top_status_date ASC;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_all_trainer_listOLD()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT clients.`user_name`, clients.`profile_picture`, trainer_profiles.`record_id`, trainer_profiles.gender

        FROM `clients`

        JOIN trainer_profiles on clients.record_id = trainer_profiles.client_look_up_id

        WHERE trainer_profiles.is_top=1
        ORDER BY trainer_profiles.top_status_date ASC;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_group_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `age_brackets`;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_all_training_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * FROM `type_of_training`;');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_trainer_profile($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT * 

        FROM `trainer_profiles`

        WHERE trainer_profiles.client_look_up_id='.$userid.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_chats_with($userid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `group_c_t_messages`.`trainer_id`,`group_c_t_messages`.`message`,`group_c_t_messages`.`on_date`,`group_c_t_messages`.`is_read_status`,`clients`.`user_name` 
        FROM `group_c_t_messages` 
        JOIN `trainer_profiles` ON `group_c_t_messages`.`trainer_id`=`trainer_profiles`.`record_id`
        JOIN `clients` ON `trainer_profiles`.`client_look_up_id`=`clients`.`record_id`
        WHERE `group_c_t_messages`.`is_viewable`=1 AND `group_c_t_messages`.`client_id`='.$userid.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_all_trainer_list_of_event($recordid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);

        $query = ('SELECT `clients`.`user_name`,`session_trainers`.`session_id`
        FROM `session_trainers` 
        JOIN `trainer_profiles` on `session_trainers`.`trainer_id` = `trainer_profiles`.`record_id`
        JOIN `clients` on `trainer_profiles`.`client_look_up_id`=`clients`.`record_id`
        WHERE `session_trainers`.`session_id`='.$recordid.';');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
