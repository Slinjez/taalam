<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    // /**
    //  * @return Blog[] Returns an array of Blog objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('b')
    ->andWhere('b.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('b.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
    public function findOneBySomeField($value): ?Blog
    {
    return $this->createQueryBuilder('b')
    ->andWhere('b.exampleField = :val')
    ->setParameter('val', $value)
    ->getQuery()
    ->getOneOrNullResult()
    ;
    }
     */
    public function save_blog($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $userid = $conn->quote($blog_details['userid']);
        $blog_title = $conn->quote($blog_details['blog_title']);
        $teaser = $conn->quote($blog_details['teaser']);
        $editor = $conn->quote($blog_details['editor']);
        $to_publish = $conn->quote($blog_details['to_publish']);

        $query = ('INSERT INTO `blog`(`blogger_id`, `title`, `teaser`, `blog`, `status`) VALUES (' . $userid . ',' . $blog_title . ',' . $teaser . ',' . $editor . ','.$to_publish.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function save_gallery($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $userid = $conn->quote($blog_details['userid']);
        $blog_title = $conn->quote($blog_details['blog_title']);

        $query = ('INSERT INTO `cms_gallery`(`image_url`, `description`) VALUES (0,0);');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
   
    
    public function save_event_tile($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $userid = $conn->quote($blog_details['userid']);
        $blog_title = $conn->quote($blog_details['blog_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('INSERT INTO `services`( `service_name`, `service_description`, `desc_picture`, `status`) VALUES ('.$blog_title.','.$editor.',"#",1)');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
       
    public function get_event_type_text($filter_record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $filter_record_id = $conn->quote($filter_record_id);

        $query = ('SELECT * FROM `services` WHERE record_id = '.$filter_record_id.'');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    

    public function update_event_tile($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            $record_id = ($blog_details['record_id']);
        $userid = $conn->quote($blog_details['userid']);
        $blog_title = $conn->quote($blog_details['blog_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `services` SET `service_name`='.$blog_title.', `service_description`='.$editor.' WHERE record_id='.$record_id.'');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        return true;
    }
    
    public function set_event_tile_location($last_id,$file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$last_id = ($last_id);
        $the_file_path = $conn->quote($file_data['file_path']);

        $query = ('UPDATE `services` SET  `desc_picture`='.$the_file_path.' WHERE `record_id`= '.$last_id.'');
        // echo $query;
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

        $query = ('UPDATE `blog` SET `thumbnail`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function set_gallery_location($blog_title,$file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $blog_title = $conn->quote($blog_title);
        $the_file_path = $conn->quote($file_data['file_path']);

        $query = ('INSERT INTO `cms_gallery`(`image_url`, `description`) VALUES ('.$the_file_path.','.$blog_title.')');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_opportunity($opportunity_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $title = $conn->quote($opportunity_details['title']);
        $userid = ($opportunity_details['userid']);
        $location = $conn->quote($opportunity_details['location']);
        $contract_legth = $conn->quote($opportunity_details['contract_legth']);
        $overview = $conn->quote($opportunity_details['overview']);
        $responsibility = $conn->quote($opportunity_details['responsibility']);
        $desirebility = $conn->quote($opportunity_details['desirebility']);
        $qualifications = $conn->quote($opportunity_details['qualifications']);
        $commitment = $conn->quote($opportunity_details['commitment']);

        $query = ('INSERT INTO `careers`(`admin_id`, `title`, `location`, `contract_length`, `over_view`, `responsibilities`, `desirability`, `qualifications`, `commitment`) VALUES (' . $userid . ',' . $title . ',' . $location . ',' . $contract_legth . ',' . $overview . ',' . $responsibility . ',' . $desirebility . ',' . $qualifications . ',' . $commitment . ')');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function get_adm_all_opportunities()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `careers` ORDER BY record_id DESC;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_byid_opportunities($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `careers` WHERE record_id=' . $record_id . ';');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_opportunity($opportunity_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($opportunity_details['record_id']);
        $status_select = $conn->quote($opportunity_details['status_select']);
        $title = $conn->quote($opportunity_details['title']);
        $userid = ($opportunity_details['userid']);
        $location = $conn->quote($opportunity_details['location']);
        $contract_legth = $conn->quote($opportunity_details['contract_legth']);
        $overview = $conn->quote($opportunity_details['overview']);
        $responsibility = $conn->quote($opportunity_details['responsibility']);
        $desirebility = $conn->quote($opportunity_details['desirebility']);
        $qualifications = $conn->quote($opportunity_details['qualifications']);
        $commitment = $conn->quote($opportunity_details['commitment']);

        $query = ('UPDATE `careers` SET `title`=' . $title . ',`location`=' . $location . ',`contract_length`=' . $contract_legth . ',`over_view`=' . $overview . ',`responsibilities`=' . $responsibility . ',`desirability`=' . $desirebility . ',`qualifications`=' . $qualifications . ',`commitment`=' . $commitment . ',`status`=' . $status_select . ' WHERE  `record_id`=' . $record_id . '');

        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function get_adm_all_blog()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `blog` ORDER BY record_id DESC;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_byid_blog($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `blog` WHERE record_id=' . $record_id . ';');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_blog($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = $conn->quote($blog_details['record_id']);
        $status_select = $conn->quote($blog_details['status_select']);
        $userid = $conn->quote($blog_details['userid']);
        $blog_title = $conn->quote($blog_details['blog_title']);
        $teaser = $conn->quote($blog_details['teaser']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `blog` SET `title`=' . $blog_title . ',`teaser`=' . $teaser . ',`blog`=' . $editor . ',`status`=' . $status_select . ' WHERE `record_id`=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function get_client_active_blog()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT blog.record_id,blog.blogger_id,admins.username,blog.title,blog.teaser,blog.date,blog.thumbnail,blog.status

        FROM `blog`

        JOIN admins on blog.blogger_id=admins.record_id

        WHERE blog.status=1
        ORDER BY record_id DESC;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_client_active_blog_recent()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT blog.record_id,blog.blogger_id,admins.username,blog.title,blog.teaser,blog.date,blog.thumbnail,blog.status

        FROM `blog`

        JOIN admins on blog.blogger_id=admins.record_id

        WHERE blog.status=1
        ORDER BY record_id DESC LIMIT 5;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_client_active_blog__byid($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT blog.record_id,blog.blogger_id,admins.username,blog.title,blog.teaser,blog.blog,blog.date,blog.thumbnail,blog.status

        FROM `blog`

        JOIN admins on blog.blogger_id=admins.record_id

        WHERE blog.record_id=' . $record_id . '
        ORDER BY record_id DESC LIMIT 5;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_client_active_job()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `careers` WHERE `status`=1 ORDER BY record_id DESC;');
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_client_active_job_byid($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //$work_email = $conn->quote($work_email);

        $query = ('SELECT * FROM `careers` WHERE record_id=' . $record_id . ';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function save_new_opp_booking($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $title = $conn->quote($blog_details['title']);
        $names = $conn->quote($blog_details['names']);
        $address = $conn->quote($blog_details['address']);
        $phone = $conn->quote($blog_details['phone']);
        $email = $conn->quote($blog_details['email']);

        $volunteer_experience = $conn->quote($blog_details['volunteer_experience']);
        $prefered_age_group = $conn->quote($blog_details['prefered_age_group']);
        $coaching_philosophy = $conn->quote($blog_details['coaching_philosophy']);
        //$availability = $conn->quote($blog_details['availability']);
        $declaration = $conn->quote($blog_details['declaration']);

        $is_studying = $conn->quote($blog_details['is_studying']);
        $is_comp_studying = $conn->quote($blog_details['is_comp_studying']);
        $is_diff_able = $conn->quote($blog_details['is_diff_able']);

        $query = ('INSERT INTO `careers_applications`(`title`, `full_names`, `address`, `mobile`, `email`, `under_taking_study`, `completed_studies`, `volunteer_exp`, `prefered_age_group`, `worked_wit_able_diff`, `coaching_phil`) VALUES (' . $title . ',' . $names . ',' . $address . ',' . $phone . ',' . $email . ',' . $is_studying . ',' . $is_comp_studying . ',' . $volunteer_experience . ',' . $prefered_age_group . ',' . $is_diff_able . ',' . $coaching_philosophy . ');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function save_new_opp_institutions($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $ins_name = $conn->quote($sv_data['ins_name']);
        $program = $conn->quote($sv_data['program']);
        $yom = $conn->quote($sv_data['yom']);


        $query = ('INSERT INTO `careers_institutions`(`career_app_id`, `institution`, `program`, `year_of_completion`) VALUES ('.$record_id.','.$ins_name.','.$program.','.$yom.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function save_new_opp_cert($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $certification_lev = $conn->quote($sv_data['certification_lev']);
        $cert = $conn->quote($sv_data['cert']);


        $query = ('INSERT INTO `careers_certification`(`career_id`, `certification`, `level`) VALUES ('.$record_id.','.$certification_lev.','.$cert.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function save_new_opp_emplyer($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $employer_name_lex = $conn->quote($sv_data['employer_name_lex']);
        $empp= $conn->quote($sv_data['empp']);
        $emp_l_r = $conn->quote($sv_data['emp_l_r']);


        $query = ('INSERT INTO `careers_employment`(`career_app_id`, `employer`, `position_and_resp`, `reason_for_leaving`) VALUES ('.$record_id.','.$employer_name_lex.','.$empp.','.$emp_l_r.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function save_new_opp_activities($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $activity_coached = $conn->quote($sv_data['activity_coached']);
        $age_val= $conn->quote($sv_data['age_val']);


        $query = ('INSERT INTO `career_sport_exp`(`career_app_id`, `activity`, `age_group`) VALUES ('.$record_id.','.$activity_coached.','.$age_val.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_new_condition_activities($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $condition_coached = $conn->quote($sv_data['condition_coached']);
        $act_Val= $conn->quote($sv_data['act_Val']);


        $query = ('INSERT INTO `career_able_diff`(`career_app_id`, `condition_exp`, `activity_exp`) VALUES ('.$record_id.','.$condition_coached.','.$act_Val.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function save_new_condition_availability($sv_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $record_id = ($sv_data['record_id']);
        $condition_coached = $conn->quote($sv_data['condition_coached']);


        $query = ('INSERT INTO `career_availability`(`career_app_id`, `id_avl`) VALUES ('.$record_id.','.$condition_coached.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    public function get_adm_all_opp_apps()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `careers_applications` order by record_id DESC;');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_all_opp_apps_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `careers_applications` WHERE record_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_adm_byid_school($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `careers_institutions` WHERE career_app_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_adm_byid_cert($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `careers_certification` WHERE career_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_byid_employment($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `careers_employment` WHERE career_app_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    // public function get_adm_byid_employment($id)
    // {
    //     $conn = $this->getEntityManager()
    //         ->getConnection();

    //     $query = ('SELECT * FROM `careers_employment` WHERE career_app_id='.$id.';');
    //     // echo $query;
    //     // exit;
    //     $sth = $conn->prepare($query);
    //     $sth->execute();
    //     $results = $sth->fetchAll();

    //     return $results;
    // }

    public function get_adm_byid_sport($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `career_sport_exp` WHERE career_app_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    // public function get_adm_byid_able_diff($id)
    // {
    //     $conn = $this->getEntityManager()
    //         ->getConnection();

    //     $query = ('SELECT * FROM `career_able_diff` WHERE career_app_id='.$id.';');
    //     // echo $query;
    //     // exit;
    //     $sth = $conn->prepare($query);
    //     $sth->execute();
    //     $results = $sth->fetchAll();

    //     return $results;
    // }

    public function get_adm_byid_able_diff($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `career_able_diff` WHERE career_app_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_byid_availability($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $query = ('SELECT * FROM `career_availability` WHERE career_app_id='.$id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_all_gallery_images()
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT * FROM `cms_gallery`');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_all_event_title_images()
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT * FROM `services`');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_all_cow_data()
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT `cms_coach_of_the_week`.`record_id`,`cms_coach_of_the_week`.`coach_id`,`clients`.`user_name`,`cms_coach_of_the_week`.`award_text`,`cms_coach_of_the_week`.`start_date`,`cms_coach_of_the_week`.`end_date`,`cms_coach_of_the_week`.`status`,`cms_coach_of_the_week`.`img_url`,`cms_coach_of_the_week`.`on_date` 

        FROM `cms_coach_of_the_week`
        
        JOIN clients ON `cms_coach_of_the_week`.`coach_id`=clients.record_id
        
        ORDER BY `cms_coach_of_the_week`.`record_id` DESC');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_active_all_cow_data()
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT `cms_coach_of_the_week`.`record_id`,`cms_coach_of_the_week`.`coach_id`,`clients`.`user_name`,`cms_coach_of_the_week`.`award_text`,`cms_coach_of_the_week`.`start_date`,`cms_coach_of_the_week`.`end_date`,`cms_coach_of_the_week`.`status`,`cms_coach_of_the_week`.`img_url`,`cms_coach_of_the_week`.`on_date` 

        FROM `cms_coach_of_the_week`
        
        JOIN clients ON `cms_coach_of_the_week`.`coach_id`=clients.record_id
        
        WHERE `cms_coach_of_the_week`.status=1
        ORDER BY `cms_coach_of_the_week`.`record_id` DESC');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function update_gallery_status($data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('UPDATE `cms_gallery` SET `status`='.$data['click_act'].' WHERE `record_id`='.$data['record_id'].';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }


    
    public function update_eventtitle_status($data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('UPDATE `services` SET `status`='.$data['click_act'].' WHERE `record_id`='.$data['record_id'].';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    
    
    public function get_eventtitle_details($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('SELECT * FROM `services` WHERE `record_id`='.$record_id.';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_all_cow_list()
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT `cms_coach_of_the_week`.`coach_id`,`clients`.`user_name`,`clients`.`email_address`,`clients`.`phone`,`cms_coach_of_the_week`.`award_text`,`cms_coach_of_the_week`.`start_date`,`cms_coach_of_the_week`.`end_date`,`cms_coach_of_the_week`.`status`,`cms_coach_of_the_week`.`img_url`,`cms_coach_of_the_week`.`on_date` 

        FROM `cms_coach_of_the_week` 
        JOIN clients ON `cms_coach_of_the_week`.`coach_id`=clients.record_id');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_all_trainer_id($coach_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT * FROM `trainer_profiles` WHERE client_look_up_id='.$coach_id.'');
        // var_dump($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function get_trainer_rating($user_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT AVG(`rating`) AS rating FROM `rating_trainer` WHERE `trainer_id`='.$user_id.'');
        // var_dump($query);
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_this_cow_list($record_id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();


        $query = ('SELECT `cms_coach_of_the_week`.`coach_id`,`clients`.`user_name`,`clients`.`email_address`,`clients`.`phone`,`cms_coach_of_the_week`.`award_text`,`cms_coach_of_the_week`.`start_date`,`cms_coach_of_the_week`.`end_date`,`cms_coach_of_the_week`.`status`,`cms_coach_of_the_week`.`img_url`,`cms_coach_of_the_week`.`on_date` 

        FROM `cms_coach_of_the_week` 
        JOIN clients ON `cms_coach_of_the_week`.`coach_id`=clients.record_id
        
        WHERE `cms_coach_of_the_week`.record_id='.$record_id.'');

        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    
    public function update_cow_status($data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            

        $query = ('UPDATE `cms_coach_of_the_week` SET `status`='.$data['click_act'].' WHERE `record_id`='.$data['record_id'].';'); 
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();

        return true;
    }

    
    public function save_contact_us($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $contact_name = $conn->quote($blog_details['contact_name']);
        $contact_email = $conn->quote($blog_details['contact_email']);
        $contact_phone = $conn->quote($blog_details['contact_phone']);
        $contact_subject = $conn->quote($blog_details['contact_subject']);
        $contact_message = $conn->quote($blog_details['contact_message']);

        $query = ('INSERT INTO `contact_us`(`user_name`, `email`, `phone`, `subject`, `body`) VALUES ('.$contact_name.','.$contact_email.','.$contact_phone.','.$contact_subject.','.$contact_message.');');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
}
