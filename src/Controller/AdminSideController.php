<?php
namespace App\Controller;

ini_set("upload_max_filesize", "300M");

use App\Controller\SessionController;
use App\Entity\Admins;
use App\Entity\Blog;
use App\Entity\Clients;
use App\Entity\CmsAboutUs;
use App\Entity\Services;
use App\Entity\TrainerFiles;
use App\Entity\TrainerProfiles;
use App\Entity\TrainingSessions;
use App\Service\OpsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminSideController extends AbstractController
{
    private $session;
    protected $projectDir;
    public function __construct(SessionInterface $session, KernelInterface $kernel)
    {
        $this->session = $session;
        $this->projectDir = $kernel->getProjectDir();
    }
    /**
     * @Route("/loginAction-adm", name="Shared loginAction-adm")
     */
    public function loginAction_adm()
    {
        //sleep(2000);
        $sescontrol = new SessionController;
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($email == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your email address.";
            return $this->json($respondWith);
            exit;
        } else if ($password == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your password.";
            return $this->json($respondWith);
            exit;
        } else {
            $password = hash('ripemd160', $password);
            $repository = $this->getDoctrine()
                ->getRepository(Admins::class);
            $validity = $repository->login($email, $password);
            $retval = array();
            if ($validity['status'] != 'ok') {
                $retval = array(
                    'status' => 'fail',
                    'messages' => $validity['msg'],
                );
                return $this->json($retval);
            } else {
                if ($validity['retval']['profpic'] == '') {
                    $pic = 'profiles/admins/5cd16043dbadc.jpg';
                } else {
                    $pic = $validity['retval']['profpic'];
                }
                $userid = $validity['retval']['uuid'];
                $username = $validity['retval']['username'];
                $role = $validity['retval']['role'];
                $profpic = $pic;
                $token = $sescontrol->getJwt($userid, $role);
                $this->session->set('dsladminuname', $username);
                $this->session->set('dsladminuid', $userid);
                $this->session->set('token', $token);
                $path = '';
                if ($role == 1) {
                    $path = '/admin-dash';
                } else if ($role == 2) {
                    $path = '/admin-dash';
                }
                $result = array(
                    'status' => 'ok',
                    'token' => $token,
                    'username' => $username,
                    'profpic' => $profpic,
                    'path' => $path,
                );
            }
            if ($result['status'] != 'ok') {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = $result['msg'];
                return $this->json($respondWith);
            } else {
                $respondWith['status'] = 'ok';
                $respondWith['messages'] = 'Welcome ' . $result["username"];
                $respondWith['vars'] = $result;
                return $this->json($respondWith);
            }
        }
    }

    /**
     * @Route("/admin-vw-all-new-sessionsx", name="admin-vw-new-all-sessionsx")
     */
    public function get_all_new_sessions_admx()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_my_sessions_adm();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_booked_date = $result['session_booked_date'];
            $session_date = $result['session_date'];
            $title = $result['title'];
            $status = $result['status'];
            $rating = $result['rating'];
            $user_name = $result['user_name'];
            $service_name = $result['service_name'];
            $trainer_id = $result['trainer_id'];
            $unit_ui_display = '';
            $repository = $this->getDoctrine()
                ->getRepository(TrainerProfiles::class);
            $trainer_details = $repository->get_this_trainer($trainer_id);
            $trainer_name = $trainer_details[0]['user_name'];
            $display_date = date('dS-M Y', strtotime($session_date));
            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($rating, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $service_name,
                $user_name,
                $trainer_name,
                $session_booked_date,
                $display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-new-sessions", name="admin-vw-new-all-sessions")
     */
    public function get_all_new_sessions_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_revamp_events_adm();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $status = $result['status'];
            $start_date = date('dS-M Y', strtotime($start_date));
            $end_date = date('dS-M Y', strtotime($end_date));

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $trainer_lists = $repository->get_session_trainers($record_id);
            $trainer_string = '';

            foreach ($trainer_lists as $trainer_list) {
                $trainer_id = $trainer_list['trainer_id'];
                $user_name = $trainer_list['user_name'];
                $trainer_string .= $user_name . ' | ';
            }

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $event_type_lists = $repository->get_session_event_type($record_id);
            $event_type_lists_string = '';

            foreach ($event_type_lists as $event_list) {
                $service_name = $event_list['service_name'];
                $event_type_lists_string .= $service_name . ' | ';
            }

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $session_title,
                $start_date,
                $trainer_string,
                $event_type_lists_string,
                $end_date,
                $location,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    
    /**
     * @Route("/admin-vw-all-new-sessionsz", name="admin-vw-new-all-sessionsz")
     */
    public function get_all_new_sessions_admz()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_revamp_events_adm();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $status = $result['status'];
            $start_date = date('dS-M Y', strtotime($start_date));
            $end_date = date('dS-M Y', strtotime($end_date));

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $trainer_lists = $repository->get_session_trainers($record_id);
            $trainer_string = '';

            foreach ($trainer_lists as $trainer_list) {
                $trainer_id = $trainer_list['trainer_id'];
                $user_name = $trainer_list['user_name'];
                $trainer_string .= $user_name . ' | ';
            }

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $event_type_lists = $repository->get_session_event_type($record_id);
            $event_type_lists_string = '';

            foreach ($event_type_lists as $event_list) {
                $service_name = $event_list['service_name'];
                $event_type_lists_string .= $service_name . ' | ';
            }

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $session_title,
                $trainer_string,
                $start_date,
                //$event_type_lists_string,
                $end_date,
                $location,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-new-sessions-by-id", name="admin-vw-new-all-sessions-by-id")
     */
    public function get_all_new_sessions_adm_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_GET['record_id'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->event_summary($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            //$record_id = $result['record_id'];
            $user_name = $result['user_name'];
            $client_id = $result['client_id'];
            $start_date = $result['booking_date'];
            $number_of_children = $result['number_of_children'];

            $start_date = date('dS-M Y', strtotime($start_date));

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $trainer_lists = $repository->get_session_trainers($record_id);
            // var_dump($trainer_lists );
            // exit;
            $trainer_string = '';

            foreach ($trainer_lists as $trainer_list) {
                $trainer_id = $trainer_list['trainer_id'];
                $user_name = $trainer_list['user_name'];
                $trainer_string .= $user_name . ' | ';
            }

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $event_type_lists = $repository->get_session_event_type($record_id);
            $event_type_lists_string = '';

            foreach ($event_type_lists as $event_list) {
                $service_name = $event_list['service_name'];
                $event_type_lists_string .= $service_name . ' | ';
            }

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status = 0);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $user_name,
                $number_of_children,
                $event_type_lists_string,
                $start_date,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-new-sessions-by-id-for-register", name="admin-vw-new-all-sessions-by-id-for-register")
     */
    public function get_all_new_sessions_adm_by_id_for_register()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_GET['record_id'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_revamp_events_child_list($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no kids yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $kids_id = $result['record_id'];
            $kidsname = $result['kidsname'];
            $date_of_birth = $result['date_of_birth'];
            $allergies = $result['allergies'];
            $medical_conditions = $result['medical_conditions'];
            $status = $result['status'];
            $parent_name = $result['parent_name'];
            $email_address = $result['email_address'];
            $phone = $result['phone'];

            $sessions_so_far = 0;

            $status_present = 0;

            $date_of_birth = date('dS-M Y', strtotime($date_of_birth));

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $attendance = $repository->get_kid_presense($record_id, $kids_id);

            if (!empty($attendance)) {
                $status_present = $attendance[0]['attendance_status'];
            }

            $ops_service = new OpsService;
            $dropdown = '';
            $check_box = '';
            $ops_service_response = $ops_service->get_attendance_params($kids_id, $status_present);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $check_box = $ops_service_response['check_box'];
            $returnarray['data'][] = array(
                $kidsname,
                $parent_name,
                $email_address,
                $phone,
                $unit_ui_display,
                $dropdown,
                $check_box,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-update-kid-attendance-status", name="admin-update-kid-attendance-status")
     */
    public function admin_update_kid_attendance_status()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $click_act_val = 0;

        $record_id = $_POST['record_id'];
        $child_id = $_POST['child_id'];
        $click_act = $_POST['click_act'];
        if ($click_act) {
            $click_act_val = 1;
        }
        $data_t_save = array(
            'record_id' => $record_id,
            'child_id' => $child_id,
            'click_act' => $click_act_val,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $pre_saved_attendance = $repository->check_existing_attendance($data_t_save);

        if (empty($pre_saved_attendance)) {
            //save
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $repository->save_attendance($data_t_save);
        } else {
            //update
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $repository->update_attendance($data_t_save);
        }
        // $repository = $this->getDoctrine()
        //     ->getRepository(Clients::class);
        // $results = $repository->update_kid_status($data_t_save);
        // var_dump($results);
        // exit;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Updated.";
        return $this->json($respondWith);

    }

    /**
     * @Route("/get-event-summary", name="get-event-summary")
     */
    public function get_all_event_summary()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['record_id'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->event_summary($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            //$record_id = $result['record_id'];
            $user_name = $result['user_name'];
            $client_id = $result['client_id'];
            $start_date = $result['booking_date'];
            $number_of_children = $result['number_of_children'];

            $start_date = date('dS-M Y', strtotime($start_date));

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $trainer_lists = $repository->get_session_trainers($record_id);
            $trainer_string = '';

            foreach ($trainer_lists as $trainer_list) {
                $trainer_id = $trainer_list['trainer_id'];
                $user_name = $trainer_list['user_name'];
                $trainer_string .= $user_name . ' | ';
            }

            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $event_type_lists = $repository->get_session_event_type($record_id);
            $event_type_lists_string = '';

            foreach ($event_type_lists as $event_list) {
                $service_name = $event_list['service_name'];
                $event_type_lists_string .= $service_name . ' | ';
            }

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status = 0);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $user_name,
                $number_of_children,
                $event_type_lists_string,
                $start_date,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-complete-sessions", name="admin-vw-all-complete-sessions")
     */
    public function get_all_complt_sessions_adm_ovr()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_revamp_events_adm_complete();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_title = $result['session_title'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $location = $result['location'];
            $status = $result['status'];
            $start_date = date('dS-M Y', strtotime($start_date));
            $end_date = date('dS-M Y', strtotime($end_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_complete_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $session_title,
                $start_date,
                $end_date,
                $location,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/update-event-status", name="update-event-status")
     */
    public function update_event_status_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['record_id'];
        $click_act = $_POST['click_act'];

        $data_t_save = array(
            'record_id' => $record_id,
            'click_act' => $click_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->update_events_adm($data_t_save);
        // var_dump($results);
        // exit;
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no events.";
            return $this->json($respondWith);
        } else {
            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Updated.";
            return $this->json($respondWith);
        }

    }

    /**
     * @Route("/admin-vw-all-ongoing-sessions", name="admin-vw-ongoing-all-sessions")
     */
    public function get_all_ongoing_sessions_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_ongoing_sessions_adm();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_booked_date = $result['session_booked_date'];
            $session_date = $result['session_date'];
            $title = $result['title'];
            $status = $result['status'];
            $rating = $result['rating'];
            $user_name = $result['user_name'];
            $service_name = $result['service_name'];
            $trainer_id = $result['trainer_id'];
            $unit_ui_display = '';
            $repository = $this->getDoctrine()
                ->getRepository(TrainerProfiles::class);
            $trainer_details = $repository->get_this_trainer($trainer_id);
            $trainer_name = $trainer_details[0]['user_name'];
            $display_date = date('dS-M Y', strtotime($session_date));
            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_session_level_params($rating, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $service_name,
                $user_name,
                $trainer_name,
                $session_booked_date,
                $display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-canceled-sessions", name="admin-vw-canceled-all-sessions")
     */
    public function get_all_canceled_sessions_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_canceled_sessions_adm();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_booked_date = $result['session_booked_date'];
            $session_date = $result['session_date'];
            $title = $result['title'];
            $status = $result['status'];
            $rating = $result['rating'];
            $user_name = $result['user_name'];
            $service_name = $result['service_name'];
            $trainer_id = $result['trainer_id'];
            $unit_ui_display = '';
            $repository = $this->getDoctrine()
                ->getRepository(TrainerProfiles::class);
            $trainer_details = $repository->get_this_trainer($trainer_id);
            $trainer_name = $trainer_details[0]['user_name'];
            $display_date = date('dS-M Y', strtotime($session_date));
            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_session_level_params($rating, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $service_name,
                $user_name,
                $trainer_name,
                $session_booked_date,
                $display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-complete-sessions_x", name="admin-vw-complete-all-sessions")
     */
    public function get_all_complete_sessions_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_complete_sessions_adm();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_booked_date = $result['session_booked_date'];
            $session_date = $result['session_date'];
            $title = $result['title'];
            $status = $result['status'];
            $rating = $result['rating'];
            $user_name = $result['user_name'];
            $service_name = $result['service_name'];
            $trainer_id = $result['trainer_id'];
            $unit_ui_display = '';
            $repository = $this->getDoctrine()
                ->getRepository(TrainerProfiles::class);
            $trainer_details = $repository->get_this_trainer($trainer_id);
            $trainer_name = $trainer_details[0]['user_name'];
            $display_date = date('dS-M Y', strtotime($session_date));
            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_session_level_params($rating, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $service_name,
                $user_name,
                $trainer_name,
                $session_booked_date,
                $display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-client-count", name="get-client-count")
     */
    public function get_client_count()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_full_client_list_count();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['client_count'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['client_count'] = $results[0]['client_count'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-full-session-count", name="get-full-session-count")
     */
    public function get_full_session_count()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_full_session_count();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['session_count'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['session_count'] = $results[0]['session_count'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-full-event-book-count", name="get-full-event-book-count")
     */
    public function get_full_event_book_count()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_full_event_book_count();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['session_count'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['session_count'] = $results[0]['session_count'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-my-trainers-view", name="get-my-trainers-view")
     */
    public function get_my_trainers_view()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_summary_trainer_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['client_count'] = 0;
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $user_name = $result['user_name'];
            $is_active = $result['is_active'];
            $date_of_joining = $result['date_of_joining'];
            $phone = $result['phone'];
            $email_address = $result['email_address'];
            $unit_ui_display = '';
            $date_of_joining = date('dS-M Y', strtotime($date_of_joining));
            $dropdown = '';
            $rating_text = '';
            if ($is_active == 0) {
                $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Pending Confirmation</span>';
            } elseif ($is_active == 1) {
            $unit_ui_display = '<span class=" badge-light btn-sm btn-block radius-30 centered-text">Confirmed</span>';
        }
        $returnarray['data'][] = array(
            'user_name' => $user_name,
            'is_active' => $is_active,
            'date_of_joining' => $date_of_joining,
            'phone' => $phone,
            'email_address' => $email_address,
            'unit_ui_display' => $unit_ui_display,
        );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Loaded";
        $respondWith['messages'] = "Prepared data.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-chart-header", name="get-chart-header")
     */
    public function get_chart_header()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $complete_results = $repository->get_graph_complete_sessions();
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $pending_results = $repository->get_graph_pending_sessions();
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['complete_results'] = $complete_results[0]['training_sessions'];
        $respondWith['pending_results'] = $pending_results[0]['training_sessions'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-chart-data", name="get-chart-data")
     */
    public function get_chart_data()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $day_lists = $this->getLastNDays(7, 'Y-m-d');
        $day_array = [];
        $pending_array = [];
        $confirmed_array = [];
        foreach ($day_lists as $day_list):
            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $pending_results = $repository->get_graph_day_activity_session_count($day_list['date'], false);
            $repository = $this->getDoctrine()
                ->getRepository(TrainingSessions::class);
            $confirmed_results = $repository->get_graph_day_activity_session_count($day_list['date'], true);
            array_push($day_array, $day_list['day_name']);
            array_push($pending_array, (int) $pending_results[0]['training_sessions']);
            array_push($confirmed_array, (int) $confirmed_results[0]['training_sessions']);
            unset($pending_results);
            unset($confirmed_results);
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['day_array'] = $day_array;
        $respondWith['pending_array'] = $pending_array;
        $respondWith['confirmed_array'] = $confirmed_array;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-all-services-list", name="get-all-services-list")
     */
    public function get_all_services_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_all_service_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-all-tiles-list", name="get-all-tiles-list")
     */
    public function get_all_tiles_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_all_service_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-all-trainer-list", name="get-all-trainer-list")
     */
    public function get_all_trainer_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_all_trainer_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-event-all-trainer-list", name="get-event-all-trainer-list")
     */
    public function get_event_all_trainer_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['record_id'];

        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_all_trainer_list_of_event($record_id);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-all-age-group-list", name="get-all-age-group-list")
     */
    public function get_all_age_group_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_all_group_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-all-age-training-list", name="get-all-training-list")
     */
    public function get_all_training_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $trainer_id = 0;
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_all_training_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-new-session", name="create-new-session")
     */
    public function create_new_session()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/events/';
        $retrieve_folder = '/uploads/heros/events/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $is_published = 0;
        $session_name = $_POST['session_name'];
        $service_select = $_POST['service_select'];
        $session_date = $_POST['session_date'];
        $session_end_date = $_POST['session_end_date'];
        $session_location = $_POST['session_location'];
        $editor = $_POST['editor'];
        $selected_trainers = $_POST['selected_trainers'];
        $num_chaperone_allowed = $_POST['num_chaperone_allowed'];
        $type_of_training = $_POST['type_of_training'];
        $age_bracket = $_POST['age_bracket'];
        $max_attendees = $_POST['max_attendees'];
        $tag_line = $_POST['tag_line'];

        $cost = $_POST['cost'];
        $num_sessions = $_POST['num_sessions'];
        $num_sessions = $_POST['num_sessions'];
        $publish = $_POST['publish'];
        // var_dump($publish);
        // exit;

        if ($publish) {
            $is_published = 1;
        }
        if ($session_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event name.";
            return $this->json($respondWith);
        } else if (empty($service_select)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select services.";
            return $this->json($respondWith);
        } else if (empty($session_date)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session start date.";
            return $this->json($respondWith);
        } else if (empty($session_end_date)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session end date.";
            return $this->json($respondWith);
        } else if (empty($session_location)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session location.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event description.";
            return $this->json($respondWith);
        } else if (empty($selected_trainers)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select trainers for the event.";
            return $this->json($respondWith);
        } else if ($num_chaperone_allowed == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter chaperones allowed.";
            return $this->json($respondWith);
        } else if ($type_of_training == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select type of training.";
            return $this->json($respondWith);
        } else if ($age_bracket == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select age bracket.";
            return $this->json($respondWith);
        } else if ($max_attendees == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select max attendees the event.";
            return $this->json($respondWith);
        } else if ($tag_line == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event tagline.";
            return $this->json($respondWith);
        } else if ($cost == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event cost.";
            return $this->json($respondWith);
        } else if ($num_sessions == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event sessions.";
            return $this->json($respondWith);
        }
        $start_date = date('Y-m-d H:i:s', strtotime($session_date));
        $end_date = date('Y-m-d H:i:s', strtotime($session_end_date));
        if ($end_date < $start_date) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Invalid entry";
            $respondWith['messages'] = "End date is earlier than the start date. I'm not letting that happen.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $event_details = array(
            'session_name' => $session_name,
            'tag_line' => $tag_line,
            'service_select' => $service_select,
            'session_date' => $session_date,
            'session_end_date' => $session_end_date,
            'session_location' => $session_location,
            'editor' => $editor,
            'num_chaperone_allowed' => $num_chaperone_allowed,
            'type_of_training' => $type_of_training,
            'age_bracket' => $age_bracket,
            'max_attendees' => $max_attendees,
            'cost' => $cost,
            'num_sessions' => $num_sessions,
            'is_published' => $is_published,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $last_id = $repository->save_event($event_details);
        $selected_trainers = explode(',', $selected_trainers);
        foreach ($selected_trainers as $selected_trainer):
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_session_trainers($last_id, $selected_trainer);
        endforeach;
        $service_selects = explode(',', $service_select);
        foreach ($service_selects as $service_select):

            if ($service_select != '') {
                $repository = $this->getDoctrine()
                    ->getRepository(Services::class);
                $repository->save_session_services($last_id, $service_select);
            }
        endforeach;
        $type_of_trainings = explode(',', $type_of_training);
        foreach ($type_of_trainings as $type_of_training):
            if ($type_of_training != '') {
                $repository = $this->getDoctrine()
                    ->getRepository(Services::class);
                $repository->save_session_trainings($last_id, $type_of_training);
            }
        endforeach;
        $age_brackets = explode(',', $age_bracket);
        foreach ($age_brackets as $age_bracket):
            if ($type_of_training != '') {
                $repository = $this->getDoctrine()
                    ->getRepository(Services::class);
                $repository->save_session_ages($last_id, $age_bracket);
            }
        endforeach;
        if (!empty($last_id)) {
            $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                //var_dump($file_data);

                //is_requirement_file
                if ($file_data['is_requirement_file'] == true) {
                    $repository = $this->getDoctrine()
                        ->getRepository(Services::class);
                    $repository->set_requirement_file($file_data);
                } else {
                    $repository = $this->getDoctrine()
                        ->getRepository(Services::class);
                    $repository->set_thumbnail_location($file_data);
                }
            endforeach;
            //exit;
        } else {
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Event created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-new-session", name="update-new-session")
     */
    public function update_new_session()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/events/';
        $retrieve_folder = '/uploads/heros/events/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $record_id = $_POST['record_id'];

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $session_name = $_POST['session_name'];
        $service_select = $_POST['service_select'];
        $session_date = $_POST['session_date'];
        $session_end_date = $_POST['session_end_date'];
        $session_location = $_POST['session_location'];
        $editor = $_POST['editor'];
        $selected_trainers = $_POST['selected_trainers'];
        $num_chaperone_allowed = $_POST['num_chaperone_allowed'];
        $type_of_training = $_POST['type_of_training'];
        $age_bracket = $_POST['age_bracket'];
        $max_attendees = $_POST['max_attendees'];
        $tag_line = $_POST['tag_line'];
        $status_select = $_POST['status_select'];
        // var_dump($status_select);
        // exit;

        $cost = $_POST['cost'];
        $num_sessions = $_POST['num_sessions'];

        if ($session_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event name.";
            return $this->json($respondWith);
        } else if (empty($status_select)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select status.";
            return $this->json($respondWith);
        } else if (empty($service_select)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select services.";
            return $this->json($respondWith);
        } else if (empty($session_date)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session start date.";
            return $this->json($respondWith);
        } else if (empty($session_end_date)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session end date.";
            return $this->json($respondWith);
        } else if (empty($session_location)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select session location.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event description.";
            return $this->json($respondWith);
        } else if (empty($selected_trainers)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select trainers for the event.";
            return $this->json($respondWith);
        } else if ($num_chaperone_allowed == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter chaperones allowed.";
            return $this->json($respondWith);
        } else if ($type_of_training == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select type of training.";
            return $this->json($respondWith);
        } else if ($age_bracket == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select age bracket.";
            return $this->json($respondWith);
        } else if ($max_attendees == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Select max attendees the event.";
            return $this->json($respondWith);
        } else if ($tag_line == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event tagline.";
            return $this->json($respondWith);
        } else if ($cost == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event cost.";
            return $this->json($respondWith);
        } else if ($num_sessions == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter event sessions.";
            return $this->json($respondWith);
        }
        $start_date = date('Y-m-d H:i:s', strtotime($session_date));
        $end_date = date('Y-m-d H:i:s', strtotime($session_end_date));
        if ($end_date < $start_date) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Invalid entry";
            $respondWith['messages'] = "End date is earlier than the start date. I'm not letting that happen.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $event_details = array(
            'record_id' => $record_id,
            'session_name' => $session_name,
            'tag_line' => $tag_line,
            'service_select' => $service_select,
            'session_date' => $session_date,
            'session_end_date' => $session_end_date,
            'session_location' => $session_location,
            'editor' => $editor,
            'num_chaperone_allowed' => $num_chaperone_allowed,
            'type_of_training' => $type_of_training,
            'age_bracket' => $age_bracket,
            'max_attendees' => $max_attendees,
            'cost' => $cost,
            'num_sessions' => $num_sessions,
            'status_select' => $status_select,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $last_id = $repository->update_event($event_details);
        $last_id = $record_id;
        $selected_trainers = explode(',', $selected_trainers);

        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $repository->delete_session_trainers($last_id);
        foreach ($selected_trainers as $selected_trainer):
            if ($selected_trainer == '') {
                continue;
            }
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_session_trainers($last_id, $selected_trainer);
        endforeach;
        $service_selects = explode(',', $service_select);

        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $repository->delete_session_services($last_id);
        foreach ($service_selects as $service_select):
            if ($service_select == '') {
                continue;
            }
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_session_services($last_id, $service_select);
        endforeach;
        $type_of_trainings = explode(',', $type_of_training);

        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $repository->delete_session_trainings($last_id);
        foreach ($type_of_trainings as $type_of_training):
            if ($type_of_training == '') {
                continue;
            }
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_session_trainings($last_id, $type_of_training);
        endforeach;
        $age_brackets = explode(',', $age_bracket);

        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $repository->delete_session_ages($last_id);
        foreach ($age_brackets as $age_bracket):
            if ($age_bracket == '') {
                continue;
            }
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_session_ages($last_id, $age_bracket);
        endforeach;
        if (!empty($last_id)) {
            $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                //is_requirement_file
                if ($file_data['is_requirement_file'] == true) {
                    $repository = $this->getDoctrine()
                        ->getRepository(Services::class);
                    $repository->set_requirement_file($file_data);
                } else {
                    $repository = $this->getDoctrine()
                        ->getRepository(Services::class);
                    $repository->set_thumbnail_location($file_data);
                }
            endforeach;
        } else {
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Event updated.";
        return $this->json($respondWith);
    }

    public function save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path)
    {
        $file_data = array();
        $countfiles = count($_FILES);
        $transaction_type = null;
        try {
            foreach ($_FILES as $key => $value) {
                $countfiles = count($value['name']);
                for ($i = 0; $i < $countfiles; $i++) {
                    //var_dump($value['name']);
                    // exit;
                    $is_requirement_file = false;

                    // if (isset($value['name'][1])) {
                    //     //var_dump($value['name'][1]);
                    //     if (strtolower($value['name'][1]) == 'requirements.pdf') {
                    //         $is_requirement_file = true;
                    //     }
                    // }

                    $theid = time() . rand();
                    $targetFile = $storeFolder . $value['name'][$i];
                    $tempFile = $value['tmp_name'][$i];
                    $file_ext = substr($targetFile, strripos($targetFile, '.'));

                    //var_dump($file_ext);
                    if (strtolower($file_ext) == '.pdf') {
                        $is_requirement_file = true;
                    }

                    try {
                        $is_moved = move_uploaded_file($tempFile, $upl_path . $theid . $file_ext);
                        //var_dump($is_moved);
                    } catch (Eception $e) {
                        //var_dump($e);
                    }
                    //exit;
                    // var_dump($value['name']);
                    // exit;

                    $file_data[] = array(
                        'record_id' => $record_id,
                        'upload_type' => $transaction_type,
                        'file_path' => $retrieve_folder . $theid . $file_ext,
                        'is_requirement_file' => $is_requirement_file,
                    );

                    
                    $is_requirement_file = false;

                }
            }
            return ($file_data);
        } catch (Exception $error) {
            return $file_data;
        }
    }

    public function getLastNDays($days, $format = 'd/m')
    {
        $m = date("m");
        $de = date("d");
        $y = date("Y");
        $dateArray = array();
        for ($i = 0; $i <= $days - 1; $i++) {
            $dateArray[] = array(
                'date' => '"' . date($format, mktime(0, 0, 0, $m, ($de - $i), $y)) . '"',
                'day_name' => date('l', mktime(0, 0, 0, $m, ($de - $i), $y)),
            );
        }
        return array_reverse($dateArray);
    }

    /**
     * @Route("/fetch-all-new-trainers", name="fetch-all-new-trainers")
     */
    public function fetch_all_new_trainers()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_full_list_new_trainers();

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
            $user_name = $result['user_name'];
            $email_address = $result['email_address'];
            $phone = $result['phone'];
            $date_of_joining = $result['date_of_joining'];

            $date_of_joining = date('dS-M Y', strtotime($date_of_joining));

            $ops_service = new OpsService;
            $dropdown = '';
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/admin-vw-new-trainers-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $returnarray['data'][] = array(
                $user_name,
                $email_address,
                $phone,
                $date_of_joining,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-trainer-profile-details", name="get-trainer-profile-details")
     */
    public function get_profile_details()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['record_id'])) {
            $token = $_POST['record_id'];
        } else if (isset($_GET['record_id'])) {
            $token = $_GET['record_id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        //$userid_array = $sescontrol->getUserid($token);
        $userid = $token;
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_client_profile($userid);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        // $client_age = floor((time() - strtotime($results[0]['date_of_birth'])) / 31556926);
        // $results[0]['client_age'] = $client_age;
        $results[0]['member_since'] = date('d-m-Y H:i', strtotime($results[0]['date_of_joining']));
        // $results[0]['dob_vw'] = Date('Y-m-d\TH:i', strtotime($results[0]['date_of_birth']));
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results[0];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-trainer-profile-details-more", name="get-trainer-profile-details-more")
     */
    public function get_profile_details_more()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['record_id'])) {
            $token = $_POST['record_id'];
        } else if (isset($_GET['record_id'])) {
            $token = $_GET['record_id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        //$userid_array = $sescontrol->getUserid($token);
        $userid = $token;
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_client_profile_more($userid);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        // $client_age = floor((time() - strtotime($results[0]['date_of_birth'])) / 31556926);
        // $results[0]['client_age'] = $client_age;
        $results[0]['date_of_birth'] = date('d-m-Y', strtotime($results[0]['date_of_birth']));
        // $results[0]['dob_vw'] = Date('Y-m-d\TH:i', strtotime($results[0]['date_of_birth']));
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results[0];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-trainer-compitency-details-more", name="get-trainer-compitency-details-more")
     */
    public function get_profile_compitency_more()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['record_id'])) {
            $token = $_POST['record_id'];
        } else if (isset($_GET['record_id'])) {
            $token = $_GET['record_id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        //$userid_array = $sescontrol->getUserid($token);
        $userid = $token;

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $trainer_client_id = $repository->get_trainer_cleint_id($userid);

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_client_compitency_more($trainer_client_id[0]['record_id']);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        // $client_age = floor((time() - strtotime($results[0]['date_of_birth'])) / 31556926);
        // $results[0]['client_age'] = $client_age;
        //$results[0]['date_of_birth'] = date('d-m-Y', strtotime($results[0]['date_of_birth']));
        // $results[0]['dob_vw'] = Date('Y-m-d\TH:i', strtotime($results[0]['date_of_birth']));
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-trainer-profile-files", name="get-trainer-profile-files")
     */
    public function get_profile_files()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['record_id'])) {
            $token = $_POST['record_id'];
        } else if (isset($_GET['record_id'])) {
            $token = $_GET['record_id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        //$userid_array = $sescontrol->getUserid($token);
        $userid = $token;
        $repository = $this->getDoctrine()
            ->getRepository(TrainerFiles::class);
        $results = $repository->get_trainer_files($userid);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $i = 0;
        $result_data = array();
        foreach ($results as $result) {
            //var_dump($result);
            //$upload_type = $result['file_path'];
            //echo 'upload type is:'.$upload_type;
            //$upl_path = $project_directory . $file_path['file_path'];
            $upl_path = 'trainer_file_download/' . $result['record_id'] . '/' . $token;
            $result_data[] = $upl_path;
            //array_push()
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $result_data;
        return $this->json($respondWith);
    }

    /**
     * @Route("/trainer_file_download/{recordid}/{token}", name="file_download")
     */
    public function file_download($recordid, $token)
    {
        $sescontrol = new SessionController;
        $trans = 0;
        if (isset($token)) {
            $token = $token;
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            echo json_encode($respondWith);
            exit;
        }

        if (isset($recordid)) {
            $trans = $recordid;
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            echo json_encode($respondWith);
            exit;
        }

        //$userid = $sescontrol->getUserid($token);

        // $repository = $this->getDoctrine()
        //     ->getRepository(Claims::class);
        // $file_paths = $repository->get_file($trans);

        $repository = $this->getDoctrine()
            ->getRepository(TrainerFiles::class);
        $file_paths = $repository->get_trainer_file_by_id($trans);
        //var_dump($file_paths);
        //exit;

        if (!empty($file_paths)) {
            foreach ($file_paths as $file_path) {
                //echo 'upload type is:'.$upload_type;
                //$upl_path = $project_directory . $file_path['file_path'];
                $upl_path = $file_path['file_path'];
                $filesystem = new Filesystem();
                $project_directory = $this->projectDir;
                $upl_path_full = $project_directory . $file_path['file_path'];
                $upl_path = $file_path['file_path'];
                $upl_path = realpath($upl_path);

                $upl_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $upl_path_full);

                $file = new File($upl_path);

                return $this->file($file);

                // rename the downloaded file
                //return $this->file($file, 'export file.pdf');

                // display the file contents in the browser instead of downloading it
                //return $this->file('welfare_claim_file.pdf', 'welfare_claim_file.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
            }
        } else {

            $respondWith['status'] = 'fail';
            $respondWith['message'] = 'invalid action';
            return $this->json($respondWith);
        }

    }

    /**
     * @Route("/confirm-trainer", name="confirm-trainer")
     */
    public function confirm_trainer()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $remarks = $_POST['remarks'];
        $record_id = $_POST['record_id'];

        $trainer_apvr = array(
            'remarks' => $remarks,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $repository->activate_trainer_acc_otp($trainer_apvr);

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $trainer_details = $repository->get_client_profile($record_id);

        $html_msg = $this->renderView('emails/email-trainer-activated.html.twig', [
            'title' => "Document Render",
            'client_name' => $trainer_details[0]['user_name'],
        ]);

        $compute_array = array(
            'username' => $trainer_details[0]['user_name'],
            'email' => $trainer_details[0]['email_address'],
            'message-html' => $html_msg,
            'message-text' => $html_msg,
            'subject' => 'Welcome to Taalam Care.',
        );

        $ops_service = new OpsService;
        $ops_service->send_generic_mail($compute_array);

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/de-confirm-trainer", name="de-confirm-trainer")
     */
    public function de_confirm_trainer()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $remarks = $_POST['remarks'];
        $record_id = $_POST['record_id'];

        $trainer_apvr = array(
            'remarks' => $remarks,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $repository->de_activate_trainer_acc_otp($trainer_apvr);

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $trainer_details = $repository->get_client_profile($record_id);

        $html_msg = $this->renderView('emails/email-trainer-rejected.html.twig', [
            'title' => "Document Render",
            'client_name' => $trainer_details[0]['user_name'],
        ]);

        $compute_array = array(
            'username' => $trainer_details[0]['user_name'],
            'email' => $trainer_details[0]['email_address'],
            'message-html' => $html_msg,
            'message-text' => $html_msg,
            'subject' => 'Apology from Taalam Care.',
        );

        $ops_service = new OpsService;
        $ops_service->send_generic_mail($compute_array);

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-new-blog", name="create-new-blog")
     */
    public function create_new_blog()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/blog/';
        $retrieve_folder = '/uploads/heros/blog/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $to_publish = 0;
        $publish = $_POST['publish'];

        if ($publish == 'true') {
            $to_publish = 1;
        }

        $blog_title = $_POST['blog_title'];
        $teaser = $_POST['teaser'];
        $editor = $_POST['editor'];
        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog title.";
            return $this->json($respondWith);
        } else if (empty($teaser)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter teaser.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'blog_title' => $blog_title,
            'teaser' => $teaser,
            'editor' => $editor,
            'userid' => $userid,
            'to_publish' => $to_publish,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id = $repository->save_blog($blog_details);

        if (!empty($last_id)) {
            $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $repository->set_thumbnail_location($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Blog created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-pdf-blog", name="create-pdf-blog")
     */
    public function create_pdf_blog()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/blog/pdf/';
        $retrieve_folder = '/uploads/heros/blog/pdf/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventpdffiles'])) {
            $file_pdf_data = $_FILES['eventpdffiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $to_publish = 0;
        $publish = $_POST['publish'];

        if ($publish == 'true') {
            $to_publish = 1;
        }

        $blog_title = $_POST['blog_title'];
        $teaser = $_POST['teaser'];
        $editor = $_POST['editor'];
        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog title.";
            return $this->json($respondWith);
        } else if (empty($teaser)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter teaser.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'blog_title' => $blog_title,
            'teaser' => $teaser,
            'editor' => $editor,
            'userid' => $userid,
            'to_publish' => $to_publish,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id = $repository->save_blog($blog_details);

        if (!empty($last_id)) {
            $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $repository->set_thumbnail_location($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Blog created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-new-gallery", name="create-new-gallery")
     */
    public function create_new_gallery()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/gallery/';
        $retrieve_folder = '/uploads/gallery/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $to_publish = 0;
        //$publish = $_POST['publish'];

        // if($publish == 'true'){
        //     $to_publish=1;
        // }

        $blog_title = $_POST['blog_title'];
        // $teaser = $_POST['teaser'];
        // $editor = $_POST['editor'];
        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter image description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'userid' => $userid,
            'blog_title' => $blog_title,
        );

        // $repository = $this->getDoctrine()
        //     ->getRepository(Blog::class);
        // $last_id = $repository->save_gallery($blog_details);

        //if (!empty($last_id)) {
        $last_id = time() . rand();
        $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
        foreach ($file_datas as $file_data):
            //var_dump($file_data);
            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $repository->set_gallery_location($blog_title, $file_data);
        endforeach;
        // } else {
        // }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Gallery created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-new-eventtile", name="create-new-eventtile")
     */
    public function create_new_eventtile()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/enenttypetiles/';
        $retrieve_folder = '/uploads/enenttypetiles/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $to_publish = 0;
        //$publish = $_POST['publish'];

        // if($publish == 'true'){
        //     $to_publish=1;
        // }

        $blog_title = $_POST['blog_title'];
        // $teaser = $_POST['teaser'];
        $editor = trim($_POST['editor']);

        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter image description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'userid' => $userid,
            'blog_title' => $blog_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id_from_db = $repository->save_event_tile($blog_details);

        //if (!empty($last_id)) {
        $last_id = time() . rand();
        $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
        foreach ($file_datas as $file_data):
            // var_dump($file_data);
            // exit;
            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $repository->set_event_tile_location($last_id_from_db, $file_data);
        endforeach;
        // } else {
        // }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Event Tile created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/edit-new-eventtile", name="edit-new-eventtile")
     */
    public function edit_new_eventtile()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/enenttypetiles/';
        $retrieve_folder = '/uploads/enenttypetiles/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $to_publish = 0;
        //$publish = $_POST['publish'];

        // if($publish == 'true'){
        //     $to_publish=1;
        // }

        $record_id = $_POST['record_id'];
        $blog_title = $_POST['blog_title'];
        // $teaser = $_POST['teaser'];
        $editor = trim($_POST['editor']);

        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter image description.";
            return $this->json($respondWith);
        }
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Error. Please reload.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'userid' => $userid,
            'blog_title' => $blog_title,
            'editor' => $editor,
        );
        $last_id_from_db = $record_id;
        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $repository->update_event_tile($blog_details);

        //if (!empty($last_id)) {
        $last_id = time() . rand();
        $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
        foreach ($file_datas as $file_data):
            // var_dump($file_data);
            // exit;
            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $repository->set_event_tile_location($last_id_from_db, $file_data);
        endforeach;
        // } else {
        // }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Event Tile updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-new-opportunities", name="create-new-opportunities")
     */
    public function create_new_opportunities()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/blog/';
        $retrieve_folder = '/uploads/heros/blog/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $title = $_POST['title'];
        $location = $_POST['location'];
        $contract_legth = $_POST['contract_legth'];
        $overview = $_POST['overview'];
        $responsibility = $_POST['responsibility'];
        $desirebility = $_POST['desirebility'];
        $qualifications = $_POST['qualifications'];
        $commitment = $_POST['commitment'];

        if ($title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter title.";
            return $this->json($respondWith);
        } else if (empty($location)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter location.";
            return $this->json($respondWith);
        } else if (empty($contract_legth)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter contract length.";
            return $this->json($respondWith);
        } else if (empty($overview)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter overview.";
            return $this->json($respondWith);
        } else if (empty($responsibility)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter responsibility.";
            return $this->json($respondWith);
        } else if (empty($desirebility)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter desirability.";
            return $this->json($respondWith);
        } else if (empty($qualifications)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter qualifications.";
            return $this->json($respondWith);
        } else if (empty($commitment)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter commitment.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $opportunity = array(
            'title' => $title,
            "userid" => $userid,
            'location' => $location,
            'contract_legth' => $contract_legth,
            'overview' => $overview,
            'responsibility' => $responsibility,
            'desirebility' => $desirebility,
            'qualifications' => $qualifications,
            'commitment' => $commitment,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id = $repository->save_opportunity($opportunity);

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Opportunity created.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-all-opportunities", name="admin-vw-all-opportunities")
     */
    public function get_all_opportunities_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_all_opportunities();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $created_on = $result['created_on'];
            $location = $result['location'];
            $contract_legth = $result['contract_length'];
            $responsibility = $result['responsibilities'];
            $desirebility = $result['desirability'];
            $qualifications = $result['qualifications'];
            $commitment = $result['commitment'];
            $status = $result['status'];
            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_opportunity_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $title,
                $created_on,
                $location,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-byid-opportunities", name="admin-vw-byid-opportunities")
     */
    public function get_byid_opportunities_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $record_id = $_POST['record_id'];

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_byid_opportunities($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $created_on = $result['created_on'];
            $location = $result['location'];
            $contract_legth = $result['contract_length'];
            $responsibility = $result['responsibilities'];
            $desirebility = $result['desirability'];
            $qualifications = $result['qualifications'];
            $commitment = $result['commitment'];
            $status = $result['status'];
            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_opportunity_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $result['unit_ui_display'] = $unit_ui_display;
            //$returnarray['data'] = $result;

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;
        //return $this->json($returnarray);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/edit-new-opportunities", name="edit-new-opportunities")
     */
    public function edit_new_opportunities()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/blog/';
        $retrieve_folder = '/uploads/heros/blog/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }

        $title = $_POST['title'];
        $location = $_POST['location'];
        $contract_legth = $_POST['contract_legth'];
        $overview = $_POST['overview'];
        $responsibility = $_POST['responsibility'];
        $desirebility = $_POST['desirebility'];
        $qualifications = $_POST['qualifications'];
        $commitment = $_POST['commitment'];

        $record_id = $_POST['record_id'];
        $status_select = $_POST['status_select'];

        if ($title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter title.";
            return $this->json($respondWith);
        } else if ($status_select == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Kindly select status.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Transaction error, please try again later.";
            return $this->json($respondWith);
        } else if (empty($location)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter location.";
            return $this->json($respondWith);
        } else if (empty($contract_legth)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter contract length.";
            return $this->json($respondWith);
        } else if (empty($overview)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter overview.";
            return $this->json($respondWith);
        } else if (empty($responsibility)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter responsibility.";
            return $this->json($respondWith);
        } else if (empty($desirebility)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter desirability.";
            return $this->json($respondWith);
        } else if (empty($qualifications)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter qualifications.";
            return $this->json($respondWith);
        } else if (empty($commitment)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter commitment.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $opportunity = array(
            'record_id' => $record_id,
            'status_select' => $status_select,
            'title' => $title,
            "userid" => $userid,
            'location' => $location,
            'contract_legth' => $contract_legth,
            'overview' => $overview,
            'responsibility' => $responsibility,
            'desirebility' => $desirebility,
            'qualifications' => $qualifications,
            'commitment' => $commitment,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id = $repository->update_opportunity($opportunity);

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Opportunity updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-all-blogs", name="admin-vw-all-blogs")
     */
    public function get_all_blogs_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_all_blog();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $created_on = $result['date'];
            $status = $result['status'];
            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_blog_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $title,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-byid-blog", name="admin-vw-byid-blog")
     */
    public function get_byid_blog_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $record_id = $_POST['record_id'];

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_byid_blog($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $created_on = $result['date'];
            $status = $result['status'];
            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_blog_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $result['unit_ui_display'] = $unit_ui_display;
            //$returnarray['data'] = $result;

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;
        //return $this->json($returnarray);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/edit-new-blog", name="edit-new-blog")
     */
    public function edit_new_blog()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/heros/blog/';
        $retrieve_folder = '/uploads/heros/blog/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $blog_title = $_POST['blog_title'];
        $teaser = $_POST['teaser'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];
        $status_select = $_POST['status_select'];

        if ($blog_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog title.";
            return $this->json($respondWith);
        } else if ($status_select == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Kindly select blog status.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($teaser)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter teaser.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter blog description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'blog_title' => $blog_title,
            'teaser' => $teaser,
            'editor' => $editor,
            'userid' => $userid,
            'status_select' => $status_select,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $last_id = $repository->update_blog($blog_details);

        if (!empty($last_id)) {
            $file_datas = $this->save_event_files($last_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $repository->set_thumbnail_location($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "Blog updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-all-opportunity-application", name="admin-vw-all-opportunity-application")
     */
    public function get_all_opportunity_application_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_all_opp_apps();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $full_names = $result['full_names'];
            $created_on = $result['app_date'];
            $mobile = $result['mobile'];
            $email = $result['email'];
            $address = $result['address'];
            $status = $result['status'];
            //$unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_opp_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $title . ' ' . $full_names,
                $created_on,
                $mobile,
                $email,
                $address,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-byid-opp-app", name="admin-vw-byid-opp-app")
     */
    public function get_byid_opp_app_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $record_id = $_POST['record_id'];

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_adm_all_opp_apps_by_id($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results_school = $repository->get_adm_byid_school($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results_certification = $repository->get_adm_byid_cert($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results_employment = $repository->get_adm_byid_employment($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results_sport = $repository->get_adm_byid_sport($record_id);

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results_availability = $repository->get_adm_byid_availability($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $title = $result['title'];
            $full_names = $result['full_names'];
            $created_on = $result['app_date'];
            $mobile = $result['mobile'];
            $email = $result['email'];
            $address = $result['address'];
            $status = $result['status'];
            //$unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_opp_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $under_taking_study = $result['under_taking_study'];
            $completed_studies = $result['completed_studies'];
            $volunteer_exp = $result['volunteer_exp'];
            $prefered_age_group = $result['prefered_age_group'];
            $worked_wit_able_diff = $result['worked_wit_able_diff'];
            $coaching_phil = $result['coaching_phil'];

            if ($under_taking_study == 0) {
                $under_taking_study = 'No';
            } else {
                $under_taking_study = 'Yes';
            }
            if ($completed_studies == 0) {
                $completed_studies = 'No';
            } else {
                $completed_studies = 'Yes';
            }
            if ($worked_wit_able_diff == 0) {
                $worked_wit_able_diff = 'No';
            } else {
                $worked_wit_able_diff = 'Yes';
            }
            if ($volunteer_exp == '' || $volunteer_exp == null) {
                $volunteer_exp = 'None';
            } else {
                $volunteer_exp = 'Yes';
            }
            if ($prefered_age_group == '' || $prefered_age_group == null) {
                $prefered_age_group = 'Not specified';
            }
            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_blog_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            //$result['unit_ui_display'] = $unit_ui_display;
            //$returnarray['data'] = $result;
            $result['under_taking_study'] = $under_taking_study;
            $result['completed_studies'] = $completed_studies;
            $result['worked_wit_able_diff'] = $worked_wit_able_diff;
            $result['volunteer_exp'] = $volunteer_exp;
            $result['prefered_age_group'] = $prefered_age_group;
            $result['created_on'] = $created_on;
            $result['unit_ui_display'] = $unit_ui_display;
            $result['results_school'] = $results_school;
            $result['results_certification'] = $results_certification;
            $result['results_employment'] = $results_employment;
            $result['results_sport'] = $results_sport;
            $result['results_availability'] = $results_availability;

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        //return $this->json($returnarray);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/admin-vw-cms-about-content", name="admin-vw-cms-about-content")
     */
    public function get_cms_about_content()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_about_content();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/update-cms-about-us", name="update-cms-about-us")
     */
    public function update_cms_about_us()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/aboutus/';
        $retrieve_folder = '/uploads/cms/aboutus/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_about($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_values($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-cms-about-mission-content", name="admin-vw-cms-about-mission-content")
     */
    public function get_cms_about_mission_content()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_about_content_mission();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/admin-vw-cms-about-current-activities", name="admin-vw-cms-about-current-activities")
     */
    public function get_cms_about_current_activities()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_upcoming_events();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/update-cms-about-us-mission", name="update-cms-about-us-mission")
     */
    public function update_cms_about_us_mission()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/aboutus/';
        $retrieve_folder = '/uploads/cms/aboutus/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_about_mission($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_values($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-cms-upcoming-events", name="update-cms-upcoming-events")
     */
    public function update_cms_upcoming_events()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/aboutus/';
        $retrieve_folder = '/uploads/cms/aboutus/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_about_mission($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_values($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * Values
     */

    /**
     * @Route("/admin-vw-cms-about-values-content", name="admin-vw-cms-about-values-content")
     */
    public function get_cms_about_values_content()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_about_content_values();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/admin-vw-cms-why-taalam-content", name="admin-vw-cms-why-taalam-content")
     */
    public function get_cms_why_taalam_content()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_about_content_values();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/update-cms-about-us-values", name="update-cms-about-us-values")
     */
    public function update_cms_about_us_values()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/aboutus/';
        $retrieve_folder = '/uploads/cms/aboutus/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_about_values($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_values($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-cms-why-taalam-values", name="update-cms-why-taalam-values")
     */
    public function update_cms_why_taalam_values()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/whytaalam/';
        $retrieve_folder = '/uploads/cms/whytaalam/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_why_taalam_values($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_why_taalam($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-all-faqs", name="admin-vw-all-faqs")
     */
    public function get_all_faqs_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_faqs();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $question,
                $answer,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-coreval", name="admin-vw-all-coreval")
     */
    public function get_all_coreval_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_core_values();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $question,
                $answer,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-whyus", name="admin-vw-all-whyus")
     */
    public function get_all_whyus_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_whyus();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $question,
                $answer,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/admin-vw-all-guide", name="admin-vw-all-guide")
     */
    public function get_all_guide_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_guide();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_guide_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $question,
                $answer,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/save-faq", name="save-faq")
     */
    public function save_faq()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->save_daq($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not saved.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/save-coreval", name="save-coreval")
     */
    public function save_coreval()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->save_coreval($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Core Value not saved.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Core Value saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/save-whyus", name="save-whyus")
     */
    public function save_whyus()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->save_whyus($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Item not saved.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Item saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/save-guide", name="save-guide")
     */
    public function save_guide()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->save_guided($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not saved.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/get_faq_by_id", name="get_faq_by_id")
     */
    public function get_faq_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = trim($_POST['record_id']);

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_faq_by_id($message_details);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'question' => $question,
                'answer' => $answer,
                'created_on' => $created_on,
                'unit_ui_display' => $unit_ui_display,
                'unit_ui_display' => $unit_ui_display,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "pulled";
        $respondWith['messages'] = "Fetched.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
        //return $this->json($returnarray);
    }

    /**
     * @Route("/get_coreval_by_id", name="get_coreval_by_id")
     */
    public function get_coreval_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = trim($_POST['record_id']);

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_core_value_by_id($message_details);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'question' => $question,
                'answer' => $answer,
                'created_on' => $created_on,
                'unit_ui_display' => $unit_ui_display,
                'unit_ui_display' => $unit_ui_display,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "pulled";
        $respondWith['messages'] = "Fetched.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
        //return $this->json($returnarray);
    }

    /**
     * @Route("/get_whyus_by_id", name="get_whyus_by_id")
     */
    public function get_whyus_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = trim($_POST['record_id']);

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_whyus_by_id($message_details);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'question' => $question,
                'answer' => $answer,
                'created_on' => $created_on,
                'unit_ui_display' => $unit_ui_display,
                'unit_ui_display' => $unit_ui_display,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "pulled";
        $respondWith['messages'] = "Fetched.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
        //return $this->json($returnarray);
    }

    /**
     * @Route("/get_guide_by_id", name="get_guide_by_id")
     */
    public function get_guide_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = trim($_POST['record_id']);

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_guide_by_id($message_details);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_faq_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'question' => $question,
                'answer' => $answer,
                'created_on' => $created_on,
                'unit_ui_display' => $unit_ui_display,
                'unit_ui_display' => $unit_ui_display,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "pulled";
        $respondWith['messages'] = "Fetched.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
        //return $this->json($returnarray);
    }

    /**
     * @Route("/update-faq", name="update-faq")
     */
    public function update_faq()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_daq($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-coreval", name="update-coreval")
     */
    public function update_coreval()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_coreval($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-whyus", name="update-whyus")
     */
    public function update_whyus()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_whyus($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Item not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Item updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-guide", name="update-guide")
     */
    public function update_guide()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_guided($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/deactivate-faq", name="deactivate-faq")
     */
    public function deactivate_faq()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_daq_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/deactivate-coreval", name="deactivate-coreval")
     */
    public function deactivate_coreval()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_coreval_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Core value not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Core value updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/deactivate-whyus", name="deactivate-whyus")
     */
    public function deactivate_whyus()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_whyus_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/deactivate-guide", name="deactivate-guide")
     */
    public function deactivate_guide()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $faq_id = trim($_POST['faq_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($faq_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'faq_id' => $faq_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_guided_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "FAQ not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "FAQ updated.";
        return $this->json($respondWith);
    }

    /**
     * HES
     */
    /**
     * @Route("/admin-vw-all-hes", name="admin-vw-all-hes")
     */
    public function get_all_hes_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_hes();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_hes_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $question,
                $answer,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/save-hes", name="save-hes")
     */
    public function save_hes()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->save_hes($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "hes not saved.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "hes saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/get_hes_by_id", name="get_hes_by_id")
     */
    public function get_hes_by_id()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = trim($_POST['record_id']);

        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_hes_by_id($message_details);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $question = $result['question'];
            $answer = $result['answer'];
            $status = $result['status'];
            $created_on = $result['on_date'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($created_on));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_hes_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'question' => $question,
                'answer' => $answer,
                'created_on' => $created_on,
                'unit_ui_display' => $unit_ui_display,
                'unit_ui_display' => $unit_ui_display,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "pulled";
        $respondWith['messages'] = "Fetched.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
        //return $this->json($returnarray);
    }

    /**
     * @Route("/update-hes", name="update-hes")
     */
    public function update_hes()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $hes_id = trim($_POST['hes_id']);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if ($hes_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($question == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Question error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($answer == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Answer error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'hes_id' => $hes_id,
            'question' => $question,
            'answer' => $answer,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_hes($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "hes not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "hes updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/deactivate-hes", name="deactivate-hes")
     */
    public function deactivate_hes()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $hes_id = trim($_POST['hes_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($hes_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'hes_id' => $hes_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_hes_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "hes not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "hes updated.";
        return $this->json($respondWith);
    }

    /**
     * CMS  PERSONAL COACHING
     */
    /**
     * @Route("/admin-vw-cms-about-percoach-content", name="admin-vw-cms-about-percoach-content")
     */
    public function get_cms_about_percoach_content()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_cms_about_content_percoach();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['data'] = $result;
            return $this->json($respondWith);
        endforeach;

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/update-cms-about-us-percoach", name="update-cms-about-us-percoach")
     */
    public function update_cms_about_us_percoach()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/public/uploads/cms/aboutus/';
        $retrieve_folder = '/uploads/cms/aboutus/';
        $thefilearry = $_FILES;
        $filesystem = new Filesystem();
        $project_directory = $this->projectDir;
        $upl_path = $project_directory . $storeFolder;
        try {
            $resp = $filesystem->mkdir($upl_path, 0777);
        } catch (IOExceptionInterface $exception) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = "Data Saved";
            $respondWith['messages'] = "An error occurred while creating your directory at " . $exception->getPath();
            $respondWith['data'] = 'error thrown';
        }
        if (isset($_FILES['eventfiles'])) {
            $file_data = $_FILES['eventfiles'];
        }
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $content_title = $_POST['content_title'];
        $editor = $_POST['editor'];
        $record_id = $_POST['record_id'];

        if ($content_title == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter cms title.";
            return $this->json($respondWith);
        } else if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Record id error, try again later.";
            return $this->json($respondWith);
        } else if (empty($editor)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Required";
            $respondWith['messages'] = "Enter CMS description.";
            return $this->json($respondWith);
        }

        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $blog_details = array(
            'record_id' => $record_id,
            'content_title' => $content_title,
            'editor' => $editor,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $last_id = $repository->update_cms_about_percoach($blog_details);

        if (!empty($record_id)) {
            $file_datas = $this->save_event_files($record_id, $storeFolder, $retrieve_folder, $upl_path);
            foreach ($file_datas as $file_data):
                $repository = $this->getDoctrine()
                    ->getRepository(CmsAboutUs::class);
                $repository->set_thumbnail_location_percoach($file_data);
            endforeach;
        } else {
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Saved";
        $respondWith['messages'] = "CMS updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/admin-vw-all-testimonials", name="admin-vw-all-testimonials")
     */
    public function get_all_testimonials_adm()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_adm_all_testimonials();

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):

            $record_id = $result['record_id'];
            $message = $result['message'];
            $rating = $result['rating'];
            $user_name = $result['user_name'];
            $on_date = $result['on_date'];
            $status = $result['status'];

            $unit_ui_display = '';
            $created_on = date('dS-M Y', strtotime($on_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_testimonials_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $user_name,
                $rating,
                $message,
                $created_on,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/update-testimonials", name="update-testimonials")
     */
    public function update_testimonials()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Session error. Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $testimonials_id = trim($_POST['testimonials_id']);
        $attr_act = trim($_POST['attr_act']);

        if ($testimonials_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record id error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($attr_act == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Attr act. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'testimonials_id' => $testimonials_id,
            'attr_act' => $attr_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $save_message_results = $repository->update_testimonials_act($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Testimonial not updated.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Testimonial updated.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/client-fetch-cow-list", name="client-fetch-cow-list")
     */
    public function get_all_cow_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_all_cow_data($userid);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $coach_id = $result['coach_id'];
            $user_name = $result['user_name'];
            $award_text = $result['award_text'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $status = $result['status'];
            $img_url = $result['img_url'];
            $on_date = $result['on_date'];

            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $coach_ids = $repository->get_all_trainer_id($coach_id);

            // var_dump($coach_ids);
            // exit;
            $user_id = $coach_ids[0]['client_look_up_id'];

            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $trainer_ratings = $repository->get_trainer_rating($coach_id);

            $trainer_rating = $trainer_ratings[0]['rating'];
            if ($trainer_rating == null) {
                $trainer_rating = 0;
            } else {
                $trainer_rating = ceil($trainer_rating);
            }
            //$on_date = (date('Y') - date('Y', strtotime($on_date)));
            $on_date = date('dS-M Y', strtotime($on_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_cow_params($record_id, $status, $img_url, $trainer_rating);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $rating_text = $ops_service_response['rating_text'];

            $returnarray['data'][] = array(
                $user_name,
                //$img_url,
                //$display_date,
                $rating_text,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-cow-full-details", name="get-cow-full-detailst")
     */
    public function get_cow_full_details()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $attr_id = $_POST['attr_id'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_this_cow_list($attr_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $attr_id;
            $coach_id = $result['coach_id'];
            $user_name = $result['user_name'];
            $award_text = $result['award_text'];
            $start_date = $result['start_date'];
            $end_date = $result['end_date'];
            $status = $result['status'];
            $img_url = $result['img_url'];
            $on_date = $result['on_date'];

            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $coach_ids = $repository->get_all_trainer_id($coach_id);

            // var_dump($coach_ids);
            // exit;
            $user_id = $coach_ids[0]['client_look_up_id'];

            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $trainer_ratings = $repository->get_trainer_rating($coach_id);

            $trainer_rating = $trainer_ratings[0]['rating'];
            if ($trainer_rating == null) {
                $trainer_rating = 0;
            } else {
                $trainer_rating = ceil($trainer_rating);
            }
            $end_date = (date('Y') - date('Y', strtotime($end_date)));
            $start_date = date('dS-M Y', strtotime($start_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_cow_params($record_id, $status, $img_url, $trainer_rating);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $rating_text = $ops_service_response['rating_text'];

            $returnarray = array(
                'status' => 'ok',
                'record_id' => $record_id,
                'user_name' => $user_name,
                'award_text' => $award_text,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'img_url' => $img_url,

                'rating_text' => $rating_text,
                'unit_ui_display' => $unit_ui_display,

                'dropdown' => $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-update-cow-status", name="client-update-cow-status")
     */
    public function client_update_cow_status()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['record_id'];
        $click_act = $_POST['click_act'];

        $data_t_save = array(
            'record_id' => $record_id,
            'click_act' => $click_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->update_cow_status($data_t_save);
        // var_dump($results);
        // exit;
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Not Updated.";
            return $this->json($respondWith);
        } else {
            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Updated.";
            return $this->json($respondWith);
        }

    }

    /**
     * @Route("/client-fetch-eventtile-list", name="client-fetch-eventtile-list")
     */
    public function client_fetch_eventtile_list()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->get_all_event_title_images($userid);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];

            $service_name = $result['service_name'];
            $image_url = $result['desc_picture'];
            // $description = $result['description'];
            //$on_date = $result['on_date'];
            $status = $result['status'];

            //$on_date = (date('Y') - date('Y', strtotime($on_date)));
            //$on_date = date('dS-M Y', strtotime($on_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_eventtitle_params($record_id, $status, $image_url);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $service_name,
                //$on_date,
                //$display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-update-eventtitle-status", name="client-update-eventtitle-status")
     */
    public function client_update_eventtitle_status()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        if (isset($_POST['token'])) {
            $token = $_POST['token'];
        } else if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['record_id'];
        $click_act = $_POST['click_act'];

        $data_t_save = array(
            'record_id' => $record_id,
            'click_act' => $click_act,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $results = $repository->update_eventtitle_status($data_t_save);
        // var_dump($results);
        // exit;
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Not Updated.";
            return $this->json($respondWith);
        } else {
            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Updated.";
            return $this->json($respondWith);
        }

    }

}
