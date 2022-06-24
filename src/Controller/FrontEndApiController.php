<?php

namespace App\Controller;

use App\Controller\SessionController;
use App\Entity\SessionsRevamp;
use App\Entity\Blog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontEndApiController extends AbstractController
{
    /**
     * @Route("/front/end/api", name="front_end_api")
     */
    public function index(): Response
    {
        return $this->render('front_end_api/index.html.twig', [
            'controller_name' => 'FrontEndApiController',
        ]);
    }

    /**
     * @Route("/get-event-list-fe-api", name="get-event-list-fe-api")
     */
    public function get_event_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $filter_record_id = 0;
        if(isset($_POST['filter_record_id'])){
            $filter_record_id = $_POST['filter_record_id'];
        }        

        if(!is_numeric($filter_record_id)){
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Invalid filter.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_all_training_list($filter_record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $event_type = $repository->get_event_type_text($filter_record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $tag_line = $result['tag_line'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $thumbnail = $result['thumbnail'];
            $max_attendee = $result['max_attendee'];
            $age_bracket = $result['age_bracket'];
            $type_of_training = $result['type_of_training'];
            $chaperone_allowed = $result['chaperone_allowed'];
            //$sessions_array = array();
            
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];


            $start_date = strtotime( $start_date );
            $start_date = date( 'd-M-Y', $start_date );
            
            $end_date = strtotime( $end_date );
            $end_date = date( 'd-M-Y', $end_date );

            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $type_of_trainings = $repository->get_all_event_training($type_of_training);

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'session_title' => $session_title,
                'tag_line' => $tag_line,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'location' => $location,
                'thumbnail' => $thumbnail,
                'max_attendee' => $max_attendee,
                'age_bracket' => $age_bracket,
                'type_of_training' => $type_of_training,
                'chaperone_allowed' => $chaperone_allowed,
                'type_of_trainings' => $type_of_trainings,
            );
        endforeach;
        
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        $respondWith['event_type_desc'] = null;
        if(isset($event_type[0])){        
            // echo ucwords($event_type[0]['service_name']);
            // echo ucfirst($event_type[0]['service_name']);
            // echo strtolower($event_type[0]['service_name']);
            $event_type[0]['service_name']=strtolower($event_type[0]['service_name']);
            //exit;    
            $respondWith['event_type_desc'] = $event_type[0];
            }
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-event-by-id-fe-api", name="get-event-by-id-fe-api")
     */
    public function get_event_by_id_fe_api()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $record_id = $_POST['record_id'];
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id not provided.";
            return $this->json($respondWith);
        }
        if (!is_numeric($record_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Invalid record id.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_view_event_training($record_id);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record not found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $tag_line = $result['tag_line'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $thumbnail = $result['thumbnail'];
            $max_attendee = $result['max_attendee'];
            $age_bracket = $result['age_bracket'];
            $type_of_training = $result['type_of_training'];
            $chaperone_allowed = $result['chaperone_allowed'];
            $description = $result['description'];

            
            $description = $result['description'];

            
            $number_of_sessions = $result['number_of_sessions'];
            $cost = $result['cost'];


            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $type_of_trainings = $repository->get_all_event_training($record_id);
            
            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $age_brackets = $repository->get_all_event_age_bracket($record_id);

            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $requirement_pdfs = $repository->get_all_event_requirement_pdf($record_id);

            if($max_attendee == 0){
                $max_attendee = 'Unlimited';
            }
            
            if($chaperone_allowed == 0){
                $chaperone_allowed = 'Unlimited';
            }
            //description

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'session_title' => $session_title,
                'tag_line' => $tag_line,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'location' => $location,
                'thumbnail' => $thumbnail,
                'max_attendee' => $max_attendee,
                'age_bracket' => $age_bracket,
                'type_of_training' => $type_of_training,
                'chaperone_allowed' => $chaperone_allowed,
                'type_of_trainings' => $type_of_trainings,
                'age_brackets' => $age_brackets,

                
                'number_of_sessions' => $number_of_sessions,
                'cost' => $cost,
                'requirement_pdfs'=>$requirement_pdfs,

                'description' => htmlspecialchars_decode($description, ENT_HTML5)
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    
    /**
     * @Route("/get-booked-event-by-id-fe-api", name="get-bookedevent-by-id-fe-api")
     */
    public function get_booked_event_by_id_fe_api()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $record_id = $_POST['record_id'];
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id not provided.";
            return $this->json($respondWith);
        }
        if (!is_numeric($record_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Invalid record id.";
            return $this->json($respondWith);
        }

        
        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $event_booking_details = $repository->get_view_event_booking($record_id);
        
        // $repository = $this->getDoctrine()
        //     ->getRepository(SessionsRevamp::class);
        // $results = $repository->get_view_event_training($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_view_event_training($event_booking_details[0]['session_id']);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record not found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $tag_line = $result['tag_line'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $thumbnail = $result['thumbnail'];
            $max_attendee = $result['max_attendee'];
            $age_bracket = $result['age_bracket'];
            $type_of_training = $result['type_of_training'];
            $chaperone_allowed = $result['chaperone_allowed'];
            $description = $result['description'];

            
            $description = $result['description'];

            
            $number_of_sessions = $result['number_of_sessions'];
            $cost = $result['cost'];


            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $type_of_trainings = $repository->get_all_event_training($record_id);
            
            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $age_brackets = $repository->get_all_event_age_bracket($record_id);

            if($max_attendee == 0){
                $max_attendee = 'Unlimited';
            }
            
            if($chaperone_allowed == 0){
                $chaperone_allowed = 'Unlimited';
            }
            //description

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'session_title' => $session_title,
                'tag_line' => $tag_line,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'location' => $location,
                'thumbnail' => $thumbnail,
                'max_attendee' => $max_attendee,
                'age_bracket' => $age_bracket,
                'type_of_training' => $type_of_training,
                'chaperone_allowed' => $chaperone_allowed,
                'type_of_trainings' => $type_of_trainings,
                'age_brackets' => $age_brackets,

                
                'number_of_sessions' => $number_of_sessions,
                'cost' => $cost,

                'description' => htmlspecialchars_decode($description, ENT_HTML5)
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-booked-event-by-id-fe-api-ovr", name="get-bookedevent-by-id-fe-api-ovr")
     */
    public function get_booked_event_by_id_fe_api_ovr()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $record_id = $_POST['record_id'];
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id not provided.";
            return $this->json($respondWith);
        }
        if (!is_numeric($record_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Invalid record id.";
            return $this->json($respondWith);
        }

        
        // $repository = $this->getDoctrine()
        //     ->getRepository(SessionsRevamp::class);
        // $event_booking_details = $repository->get_view_event_booking($record_id);
        
        // $repository = $this->getDoctrine()
        //     ->getRepository(SessionsRevamp::class);
        // $results = $repository->get_view_event_training($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_view_event_training($record_id);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record not found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $tag_line = $result['tag_line'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $thumbnail = $result['thumbnail'];
            $max_attendee = $result['max_attendee'];
            $age_bracket = $result['age_bracket'];
            $type_of_training = $result['type_of_training'];
            $chaperone_allowed = $result['chaperone_allowed'];
            $description = $result['description'];

            
            $description = $result['description'];

            
            $number_of_sessions = $result['number_of_sessions'];
            $cost = $result['cost'];


            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $type_of_trainings = $repository->get_all_event_training($record_id);
            
            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $age_brackets = $repository->get_all_event_age_bracket($record_id);

            if($max_attendee == 0){
                $max_attendee = 'Unlimited';
            }
            
            if($chaperone_allowed == 0){
                $chaperone_allowed = 'Unlimited';
            }
            //description

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'session_title' => $session_title,
                'tag_line' => $tag_line,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'location' => $location,
                'thumbnail' => $thumbnail,
                'max_attendee' => $max_attendee,
                'age_bracket' => $age_bracket,
                'type_of_training' => $type_of_training,
                'chaperone_allowed' => $chaperone_allowed,
                'type_of_trainings' => $type_of_trainings,
                'age_brackets' => $age_brackets,

                
                'number_of_sessions' => $number_of_sessions,
                'cost' => $cost,

                'description' => htmlspecialchars_decode($description, ENT_HTML5)
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    
    /**
     * @Route("/get-blog-list-fe-api", name="get-blog-list-fe-api")
     */
    public function get_blog_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_client_active_blog();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $username =  $result['username'];
            $blogger_id = $result['blogger_id'];
            $title = $result['title'];
            $teaser = $result['teaser'];
            $start_date = $result['date'];
            $thumbnail = $result['thumbnail'];
            $status = $result['status'];
            
            $start_date = date('dS-M Y', strtotime($start_date));

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'blogger_name' => $username,
                'blogger_id' => $blogger_id,
                'title' => $title,
                'teaser' => $teaser,
                'start_date' => $start_date,
                'thumbnail' => $thumbnail,
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    
    /**
     * @Route("/get-blog-list-fe-recent-api", name="get-blog-list-fe-recent-api")
     */
    public function get_blog_list_recent()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_client_active_blog_recent();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $username =  $result['username'];
            $blogger_id = $result['blogger_id'];
            $title = $result['title'];
            $teaser = $result['teaser'];
            $start_date = $result['date'];
            $thumbnail = $result['thumbnail'];
            $status = $result['status'];
            
            $start_date = date('dS-M Y', strtotime($start_date));

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'blogger_name' => $username,
                'blogger_id' => $blogger_id,
                'title' => $title,
                'teaser' => $teaser,
                'start_date' => $start_date,
                'thumbnail' => $thumbnail,
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    
    /**
     * @Route("/get-blog-by-id-fe-api", name="get-blog-by-id-fe-api")
     */
    public function get_blog_by_id_fe_api()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $record_id = $_POST['record_id'];
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id not provided.";
            return $this->json($respondWith);
        }
        if (!is_numeric($record_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Invalid record id.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_client_active_blog__byid($record_id);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record not found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $username =  $result['username'];
            $blogger_id = $result['blogger_id'];
            $title = $result['title'];
            $blog = $result['blog'];
            $teaser = $result['teaser'];
            $start_date = $result['date'];
            $thumbnail = $result['thumbnail'];
            $status = $result['status'];
            
            $start_date = date('dS-M Y', strtotime($start_date));

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'blogger_name' => $username,
                'blogger_id' => $blogger_id,
                'title' => $title,
                'blog' => $blog,
                'teaser' => $teaser,
                'start_date' => $start_date,
                'thumbnail' => $thumbnail,
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-job-list-fe-api", name="get-job-list-fe-api")
     */
    public function get_job_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_client_active_job();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title =  $result['title'];
            $location = $result['location'];
            $contract_length = $result['contract_length'];
            $over_view = $result['over_view'];
            $created_on = $result['created_on'];
            
            $start_date = date('dS-M Y', strtotime($created_on));

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'title' => $title,
                'over_view'=>$over_view,
                'location_job' => $location,
                'contract_length' => $contract_length,
                'start_date' => $start_date,
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }
    
    
    /**
     * @Route("/get-opportunity-by-id-fe-api", name="get-opportunity-by-id-fe-api")
     */
    public function get_opportunity_by_id_fe_api()
    {
        //sleep(10000);
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';

        $record_id = $_POST['record_id'];
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id not provided.";
            return $this->json($respondWith);
        }
        if (!is_numeric($record_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Invalid record id.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_client_active_job_byid($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title =  $result['title'];
            $location = $result['location'];
            $contract_length = $result['contract_length'];
            $over_view = $result['over_view'];
            $created_on = $result['created_on'];

            $responsibilities = $result['responsibilities'];
            $desirability = $result['desirability'];
            $qualifications = $result['qualifications'];
            $commitment = $result['commitment'];
            
            $start_date = date('dS-M Y', strtotime($created_on));

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'title' => $title,
                'location_job' => $location,
                'contract_length' => $contract_length,
                'start_date' => $start_date,

                'over_view' => $over_view,
                'responsibilities' => $responsibilities,
                'desirability' => $desirability,
                'qualifications' => $qualifications,
                'commitment' => $commitment,
            );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Resultset";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }
    
}
