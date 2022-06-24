<?php

namespace App\Repository;

use App\Entity\CmsAboutUs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CmsAboutUs|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsAboutUs|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsAboutUs[]    findAll()
 * @method CmsAboutUs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsAboutUsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsAboutUs::class);
    }

    // /**
    //  * @return CmsAboutUs[] Returns an array of CmsAboutUs objects
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
    public function findOneBySomeField($value): ?CmsAboutUs
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    public function get_about_us()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_about_us_why_taalam()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_why_taalam` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_about_us_event_cms()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_blog_current_activities` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_cms_about_content()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_cms_about($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($blog_details['record_id']);
        $content_title = $conn->quote($blog_details['content_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `cms_about_us` SET `title`='.$content_title .',`body`='.$editor .' WHERE `record_id`='.$record_id.';');
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

            $record_id = ($file_data['record_id']);
            $the_file_path = $conn->quote($file_data['file_path']);
    
            $query = ('UPDATE `cms_about_us` SET `side_image`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    /**
     * Mission
     */
    
    public function get_mission()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us_mission` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_adm_cms_about_content_mission()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us_mission` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_adm_cms_upcoming_events()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_blog_current_activities` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function update_cms_about_mission($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($blog_details['record_id']);
        $content_title = $conn->quote($blog_details['content_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `cms_about_us_mission` SET `title`='.$content_title .',`body`='.$editor .' WHERE `record_id`='.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function set_thumbnail_location_mission($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

            $record_id = ($file_data['record_id']);
            $the_file_path = $conn->quote($file_data['file_path']);
    
            $query = ('UPDATE `cms_about_us_mission` SET `side_image`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    /**
     * Values
     */
    
    public function get_values()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us_values` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_adm_cms_about_content_values()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_about_us_values` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function update_cms_about_values($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($blog_details['record_id']);
        $content_title = $conn->quote($blog_details['content_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `cms_about_us_values` SET `title`='.$content_title .',`body`='.$editor .' WHERE `record_id`='.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function set_thumbnail_location_values($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

            $record_id = ($file_data['record_id']);
            $the_file_path = $conn->quote($file_data['file_path']);
    
            $query = ('UPDATE `cms_about_us_values` SET `side_image`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function set_thumbnail_location_why_taalam($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

            $record_id = ($file_data['record_id']);
            $the_file_path = $conn->quote($file_data['file_path']);
    
            $query = ('UPDATE `cms_why_taalam` SET `side_image`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    /**
     * FAQ
     */
    public function get_faqs()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `faqs`;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_faqs_active_only()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `faqs` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }    

    public function save_daq($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `faqs`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function get_faq_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `faqs` WHERE record_id='.$id['record_id'].';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function update_faq($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `faqs`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function update_daq($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('UPDATE `faqs` SET `question`='.$question.',`answer`='.$answer.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }
    
    public function update_daq_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `faqs` SET `status`='.$attr_act.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }

     
    /**
     * hes
     */
    public function get_hes()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_health_and_safety`;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_hes_active_only()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_health_and_safety` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    
    public function get_hes_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_health_and_safety` WHERE record_id='.$id['record_id'].';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function save_hes($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `cms_health_and_safety`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function update_hes($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $hes = ($file_data['hes_id']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('UPDATE `cms_health_and_safety` SET `question`='.$question.',`answer`='.$answer.' WHERE `record_id`='.$hes.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }
    
    public function update_hes_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $hes = ($file_data['hes_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `cms_health_and_safety` SET `status`='.$attr_act.' WHERE `record_id`='.$hes.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }

    /**
     * personal coaching
     */
    
    public function get_personal_coaching()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_personal_coaching` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_cms_about_content_percoach()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `cms_personal_coaching` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function update_cms_about_percoach($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($blog_details['record_id']);
        $content_title = $conn->quote($blog_details['content_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `cms_personal_coaching` SET `title`='.$content_title .',`body`='.$editor .' WHERE `record_id`='.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function set_thumbnail_location_percoach($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

            $record_id = ($file_data['record_id']);
            $the_file_path = $conn->quote($file_data['file_path']);
    
            $query = ('UPDATE `cms_personal_coaching` SET `side_image`=' . $the_file_path . ' WHERE `record_id`=' . $record_id . '');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    /**
     * Testimonials
     */
    
    public function get_testimonials()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

    
            $query = ('SELECT testimonials.`message`,testimonials.rating,clients.user_name 

            FROM `testimonials` 
            JOIN clients on testimonials.client_id=clients.record_id
            WHERE testimonials.status=1');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function get_adm_all_testimonials()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

    
            $query = ('SELECT testimonials.`record_id`,testimonials.`message`,testimonials.rating,testimonials.status,clients.user_name,testimonials.on_date 

            FROM `testimonials` 
            JOIN clients on testimonials.client_id=clients.record_id
            ');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function update_testimonials_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $testimonials_id = ($file_data['testimonials_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `testimonials` SET `status`='.$attr_act.' WHERE `record_id`='.$testimonials_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }

    /**
     * Gallery
     */

    public function get_gallery()
    {
        $conn = $this->getEntityManager()
            ->getConnection();

    
            $query = ('SELECT * FROM `cms_gallery` WHERE `status`=1');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }



    








    
    /**
     * GUIDE
     */
    public function get_guide()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `guide`;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_guide_active_only()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `guide` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    

    public function save_guided($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `guide`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function get_guide_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `guide` WHERE record_id='.$id['record_id'].';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function update_guide($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `guide`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function update_guided($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('UPDATE `guide` SET `question`='.$question.',`answer`='.$answer.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }
    
    public function update_guided_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `guide` SET `status`='.$attr_act.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }









    
    
    /**
     * whyus
     */
    public function get_whyus()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `infographic`;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    
    public function get_whyus_active_only()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `infographic` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    

    public function save_whyus($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `infographic`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

    
    public function get_whyus_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `infographic` WHERE record_id='.$id['record_id'].';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    // public function update_whyus($file_data)
    // {
    //     $conn = $this->getEntityManager()
    //         ->getConnection();
            
    //     $userid = ($file_data['userid']);
    //     $question = $conn->quote($file_data['question']);
    //     $answer = $conn->quote($file_data['answer']);

        
    //     $query = ('INSERT INTO `infographic`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
    //     $sth = $conn->prepare($query);
    //     $sth->execute();
        
    //     $rowsAffected = $conn->lastInsertId();

    //     return $rowsAffected;
    // }
    
    public function update_whyus($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('UPDATE `infographic` SET `question`='.$question.',`answer`='.$answer.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }
    
    public function update_whyus_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `infographic` SET `status`='.$attr_act.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }








    /**
     * COREVAL
     */
    public function get_core_values()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `core_values`;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function get_core_values_active_only()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `core_values` WHERE status=1;');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }    

    public function save_coreval($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `core_values`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function get_core_value_by_id($id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        //$email = $conn->quote($email);
        
        $query = ('SELECT * FROM `core_values` WHERE record_id='.$id['record_id'].';');        
        
        $sth = $conn->prepare($query);
        $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
    
    public function update_core_value($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $userid = ($file_data['userid']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('INSERT INTO `core_values`(`question`, `answer`) VALUES ('.$question.','.$answer.')');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }
    
    public function update_coreval($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $question = $conn->quote($file_data['question']);
        $answer = $conn->quote($file_data['answer']);

        
        $query = ('UPDATE `core_values` SET `question`='.$question.',`answer`='.$answer.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }
    
    public function update_coreval_act($file_data)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
            
        $faq_id = ($file_data['faq_id']);
        $attr_act = $conn->quote($file_data['attr_act']);

        
        $query = ('UPDATE `core_values` SET `status`='.$attr_act.' WHERE `record_id`='.$faq_id.'');
        
        $sth = $conn->prepare($query);
        $sth->execute();
        

        return true;
    }

    
    public function update_cms_why_taalam_values($blog_details)
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $record_id = ($blog_details['record_id']);
        $content_title = $conn->quote($blog_details['content_title']);
        $editor = $conn->quote($blog_details['editor']);

        $query = ('UPDATE `cms_why_taalam` SET `title`='.$content_title .',`body`='.$editor .' WHERE `record_id`='.$record_id.';');
        // echo $query;
        // exit;
        $sth = $conn->prepare($query);
        $sth->execute();
        $rowsAffected = $conn->lastInsertId();

        return $rowsAffected;
    }

}
