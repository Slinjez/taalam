<?php

namespace App\Repository;

use App\Entity\Clients;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clients|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clients|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clients[]    findAll()
 * @method Clients[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clients::class);
    }

    // /**
    //  * @return Clients[] Returns an array of Clients objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Clients
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    
    public function login($email, $password)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $email = $conn->quote($email);
        $query = ('SELECT * FROM `clients` WHERE LOWER(`email_address`)=LOWER(' . $email . ');');
       
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        $retVal = array();

        if (empty($results)) {
            $retVal = array(
                'status' => 'fail',
                'msg' => 'Account Not Found',
            );
            return $retVal;
            exit;
        } else {
            $profpic = '';
            if ($results[0]['role'] == 2 && $results[0]['traiver_verification_status'] != 1) {
                $retVal = array(
                    'status' => 'fail',
                    'msg' => 'Your profile is still under review.',
                );
                return $retVal;
            }
            if ($results[0]['password'] == $password) {
                if ($results[0]['profpic'] = '') {
                    $profpic = '8.jpg';
                } else {
                    $profpic = $results[0]['profpic'];
                }
                $retVal = array(
                    'status' => 'ok',
                    'retval' => array(
                        'uuid' => $results[0]['record_id'],
                        'username' => $results[0]['user_name'],
                        'email' => $results[0]['email_address'],
                        'role' => $results[0]['role'],
                        'profpic' => '',
                    ),
                );
                return $retVal;
            } else {
                $retVal = array(
                    'status' => 'fail',
                    'msg' => 'invalid credentials',
                );
                return $retVal;
            }
        }
    }

    
    public function confirm_not_already_registered($email)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $email = $conn->quote($email);
        
        $query = ('SELECT * FROM `clients` WHERE LOWER(`email_address`) = LOWER(' . $email . ');');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function add_login_info($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $user_name = $conn->quote($user['user_name']);
        $user_email = $conn->quote($user['user_email']);
        $password = $conn->quote($user['password']);
        $mobile_number = $conn->quote($user['mobile_number']);
        $OTP = $conn->quote($user['OTP']);

        $query = ('INSERT INTO `clients`( `user_name`, `email_address`, `phone`, `password`, `is_active`, `temp_otp`) VALUES (' . $user['user_acc'] . ',' . $user_name . ',' . $user_work_mail . ',' . $user_alt_mail . ',' . $password . ','.$user_department.',0,'.$OTP.');'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function get_client_profile($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT clients.`record_id`,clients.`user_name`,clients.`email_address`,clients.`phone`,clients.is_active,clients.`date_of_joining`,clients.`is_trainer`,clients.`profile_picture`
        FROM `clients` 
        WHERE clients.record_id='.$user_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function update_client_name($user_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $user_id = ($user_details['userid']);
        $client_full_name = $conn->quote($user_details['client_full_name']);

        $query = ('UPDATE `clients` SET `user_name`='.$client_full_name.' WHERE record_id = '.$user_id.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }
    
    public function update_client_profile($user_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $user_id = ($user_details['userid']);
        $client_full_name = $conn->quote($user_details['client_full_name']);
        $client_phone = $conn->quote($user_details['client_phone']);
        $client_location = $conn->quote($user_details['client_location']);
        $client_nationality = $conn->quote($user_details['client_nationality']);
        $client_dob = $conn->quote($user_details['client_dob']);
        $client_twitter = $conn->quote($user_details['client_twitter']);
        $client_insta = $conn->quote($user_details['client_insta']);
        $client_fb = $conn->quote($user_details['client_fb']);
        $client_bio_field = $conn->quote($user_details['client_bio_field']);

        $query = ('UPDATE `client_profiles` SET `date_of_birth`='.$client_dob.',`social_link_twitter`='.$client_twitter.',`social_link_facebook`='.$client_fb.',`social_link_insta`='.$client_insta.',`location`='.$client_location.',`nationality`='.$client_nationality.',`bio`='.$client_bio_field.',`mobile`='.$client_phone.' WHERE `client_id`= '.$user_id.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    
    public function get_full_client_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $query = ('SELECT * FROM `clients` WHERE is_trainer=0;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_full_trainer_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $query = ('SELECT * FROM `clients` WHERE is_trainer=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_full_client_list_count()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $query = ('SELECT COUNT(*) AS client_count FROM `clients` WHERE is_trainer=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_summary_trainer_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $query = ('SELECT * FROM `clients` WHERE is_trainer=1 ORDER BY record_id DESC LIMIT 5;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function verify_registration_reset($work_email)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $work_email = $conn->quote($work_email);
        $query = ('SELECT * FROM `clients` WHERE LOWER(`email_address`) = LOWER(' . $work_email . ');');  
            
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_user_password($save_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $work_email = $conn->quote($save_data['work_email']);
        $ps1 = $conn->quote($save_data['ps1']);
        $OTP = $conn->quote($save_data['OTP']);

        $query = ('UPDATE `clients` SET `password`='.$ps1.', `is_active`=0,`temp_otp`='.$OTP.',`otp_time`=NOW() WHERE `email_address`='.$work_email.';');    
            
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    public function get_acc_otp($work_email)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $work_email = $conn->quote($work_email);
        
        $query = ('SELECT `temp_otp`, `otp_time` FROM `clients` WHERE LOWER(`email_address`) = LOWER(' . $work_email . ');');        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function activate_acc_otp($work_email)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $work_email = $conn->quote($work_email);

        $query = ('UPDATE `clients` SET `traiver_verification_status`=1,`is_active`=1  WHERE LOWER(`email_address`)=LOWER('.$work_email.');');  

        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }
    
    public function get_full_list_new_trainers()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        
        $query = ('SELECT * FROM `clients` WHERE is_trainer=1 AND traiver_verification_status=0;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function activate_trainer_acc_otp($record_desc)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($record_desc['record_id']);
        $remarks = $conn->quote($record_desc['remarks']);

        $query = ('UPDATE `clients` SET `is_active`=1,`traiver_verification_status`=1,`trainer_verification_remarks`='.$remarks.'  WHERE `record_id`='.$record_id.';');  

        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    public function de_activate_trainer_acc_otp($record_desc)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($record_desc['record_id']);
        $remarks = $conn->quote($record_desc['remarks']);

        $query = ('UPDATE `clients` SET `is_active`=0, `traiver_verification_status`=0, `trainer_verification_remarks`='.$remarks.'  WHERE `record_id`='.$record_id.';');  

        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    public function create_kid_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = $conn->quote($user['userid']);
        $client_full_name = $conn->quote($user['client_full_name']);
        $client_dob = $conn->quote($user['client_dob']);
        $client_allergies = $conn->quote($user['client_allergies']);
        $client_medical_conditions = $conn->quote($user['client_medical_conditions']);

        
        $client_special_needs = $conn->quote($user['client_special_needs']);
        $client_behavioral_conditions = $conn->quote($user['client_behavioral_conditions']);


        $query = ('INSERT INTO `member_kids`( `member_id`, `kidsname`, `date_of_birth`, `allergies`, `medical_conditions`,`special_needs`,`behavioral_conditions`, `status`) VALUES ('.$userid.','.$client_full_name.','.$client_dob.','.$client_allergies.','.$client_medical_conditions.','.$client_special_needs.','.$client_behavioral_conditions.',1);'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function create_kid_chaperone($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = $conn->quote($user['userid']);
        $client_full_name = $conn->quote($user['client_full_name']);
        $relationship = $conn->quote($user['relationship']);
        $phonenumber = $conn->quote($user['phonenumber']);
        $email = $conn->quote($user['email']);        
        $location = $conn->quote($user['location']);
        

        $query = ('INSERT INTO `member_chaperone`(`member_id`, `name`, `relationship`, `phonenumber`, `email`, `location`) VALUES ('.$userid.','.$client_full_name.','.$relationship.','.$phonenumber.','.$email.','.$location.')'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function update_kid_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = ($user['record_id']);
        $userid = $conn->quote($user['userid']);
        $client_full_name = $conn->quote($user['client_full_name']);
        $client_dob = $conn->quote($user['client_dob']);
        $client_allergies = $conn->quote($user['client_allergies']);
        $client_medical_conditions = $conn->quote($user['client_medical_conditions']);

        
        $client_special_needs = $conn->quote($user['client_special_needs']);
        $client_behavioral_conditions = $conn->quote($user['client_behavioral_conditions']);


        $query = ('UPDATE `member_kids` SET `kidsname`='.$client_full_name.',`date_of_birth`='.$client_dob.',`allergies`='.$client_allergies.',`medical_conditions`='.$client_medical_conditions.',`special_needs`='.$client_special_needs.',`behavioral_conditions`='.$client_behavioral_conditions.' WHERE `record_id`='.$record_id.''); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        //$rowsAffected = $conn->lastInsertId();

        return 1;
    }

    
    public function update_chaperone_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = ($user['record_id']);
        $userid = $conn->quote($user['userid']);
        $client_full_name = $conn->quote($user['client_full_name']);
        $relationship = $conn->quote($user['relationship']);
        $phonenumber = $conn->quote($user['phonenumber']);
        $email = $conn->quote($user['email']);
        $location = $conn->quote($user['location']);

        

        $query = ('UPDATE `member_chaperone` SET `name`='.$client_full_name.',`relationship`='.$relationship.',`phonenumber`='.$phonenumber.',`email`='.$email.',`location`='.$location.' WHERE `record_id`='.$record_id.''); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        //$rowsAffected = $conn->lastInsertId();

        return 1;
    }

    public function create_kid_needs($kid,$condition)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $query = ('INSERT INTO `member_kid_special_needs`( `kid_id`, `need`) VALUES ('.$kid.','.$condition.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function clear_kid_need($kid)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $query = ('DELETE FROM `member_kid_special_needs` WHERE `kid_id`='.$kid.';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        //$rowsAffected = $conn->lastInsertId();

        return 1;
    }

    public function get_kid_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_kids` WHERE member_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }


    public function get_full_kid_details()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT `member_kids`.`record_id`,`member_kids`.`kidsname`,`member_kids`.`member_id`,`clients`.`user_name`,`member_kids`.`date_of_birth`,`member_kids`.`allergies`,`member_kids`.`medical_conditions`,`member_kids`.`special_needs`,`member_kids`.`behavioral_conditions`,`member_kids`.`status`,`member_kids`.`created_date` 

        FROM `member_kids` 
        JOIN `clients` ON `clients`.`record_id`=`member_kids`.`member_id`;'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_chaperone_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_chaperone` WHERE member_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_kid_fl_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_kids` WHERE record_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_chaperone_fl_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_chaperone` WHERE record_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_chap_fl_detailxs($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_chaperone` WHERE record_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }


    public function get_kid_fl_special_needs_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT cms_condition_list.condition_name 
        FROM `member_kid_special_needs` 
        JOIN cms_condition_list ON member_kid_special_needs.need=cms_condition_list.record_id
        WHERE member_kid_special_needs.kid_id='.$user.';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function update_kid_status($data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('UPDATE `member_kids` SET `status`='.$data['click_act'].' WHERE `record_id`='.$data['record_id'].';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    public function update_chaperone_status($data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('UPDATE `member_chaperone` SET `status`='.$data['click_act'].' WHERE `record_id`='.$data['record_id'].';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    
    public function get_active_kid_details($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `member_kids` WHERE member_id='.$user.' AND `status`=1;'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_active_condition_list($user)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `cms_condition_list` WHERE `status`=1;'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function save_kid_session($last_id, $kid_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('INSERT INTO `session_kids`( `session_id`, `child_id`) VALUES ('.$last_id.','.$kid_id.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_trainer_rating($record_desc)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
            
        $userid = $conn->quote($record_desc['userid']);
        $trainer_id = $conn->quote($record_desc['trainer_id']);
        $recordid = $conn->quote($record_desc['recordid']);
        $stars = $conn->quote($record_desc['stars']);
        $remarks = $conn->quote($record_desc['extra_info']);

        $query = ('INSERT INTO `rating_trainer`( `session_id`, `client_id`, `trainer_id`, `remarks`, `rating`) VALUES ('.$recordid.','.$userid.','.$trainer_id.','.$remarks.','.$stars.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function check_existing_attendance($record_desc)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `session_kids_register` WHERE `child_id`='.$record_desc['child_id'].' AND `session_id`='.$record_desc['record_id'].';'); 

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function save_attendance($record_desc)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
            
        $record_id = ($record_desc['record_id']);
        $child_id = ($record_desc['child_id']);
        $click_act = ($record_desc['click_act']);
        //$remarks = $conn->quote($record_desc['extra_info']);

        $query = ('INSERT INTO `session_kids_register`(`session_id`, `child_id`, `attendance_status`) VALUES ('. $record_id.','.$child_id.','.$click_act.');'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function update_attendance($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $record_id = $conn->quote($file_data['record_id']);
        $child_id = $conn->quote($file_data['child_id']);
        $click_act = $conn->quote($file_data['click_act']);

        
        $query = ('UPDATE `session_kids_register` SET `attendance_status`='.$click_act.' WHERE `session_id`='.$record_id.' AND `child_id`='.$child_id.'');
        // var_dump($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    
    public function save_chat_message($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $trainer_id = ($file_data['trainer_id']);
        $recordid = ($file_data['recordid']);
        $extra_info = $conn->quote($file_data['extra_info']);

        
        $query = ('INSERT INTO `group_c_t_messages`(`client_id`, `trainer_id`, `message`) VALUES ('.$userid.','.$trainer_id.','.$extra_info.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function get_client_profile_more($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT *
        FROM `client_profiles` 
        WHERE client_profiles.client_id='.$user_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_trainer_cleint_id($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT `record_id`
        FROM `client_profiles` 
        WHERE client_profiles.client_id='.$user_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_client_compitency_more($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT `trainer_competencies`.`competency` 
        FROM `trainer_competencies` 
        WHERE trainer_competencies.trainer_id='.$user_id.';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
