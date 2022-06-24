<?php
namespace App\Controller;

use App\Controller\SessionController;
use App\Entity\Blog;
use App\Entity\Clients;
use App\Entity\EventBookings;
use App\Entity\Services;
use App\Entity\SessionsRevamp;
use App\Entity\TrainerProfiles;
use App\Entity\TrainingSessions;
use App\Service\OpsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientSideController extends AbstractController
{
    private $session;
    protected $projectDir;
    public function __construct(SessionInterface $session, KernelInterface $kernel)
    {
        $this->session = $session;
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     * @Route("/loginAction", name="Shared loginAction")
     */
    public function loginAction()
    {
        sleep(0.5);
        $sescontrol = new SessionController;
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($email == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your email address.";
            return $this->json($respondWith);
            //exit;
        } else if ($password == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your password.";
            return $this->json($respondWith);
            //exit;
        } else {
            $password = hash('ripemd160', $password);
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
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
                    $pic = '8.jpg';
                } else {
                    $pic = $validity['retval']['profpic'];
                }
                $userid = $validity['retval']['uuid'];
                $username = $validity['retval']['username'];
                $comment = strlen($username) > 15 ? substr($iusernamen, 0, 15) . "..." : $username;
                $profpic = $pic;
                $role = $validity['retval']['role'];
                $token = $sescontrol->getJwt($userid, $role);
                $this->session->set('dropshopuname', $username);
                $this->session->set('dropshopuid', $userid);
                $this->session->set('token', $token);
                $path = '';
                // var_dump($role);
                // //exit;
                if ($role == 1) {
                    $path = '/client-dash';
                } else if ($role == 2) {
                    $path = '/trainer-dash';
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = 'Invalid login. Kindly contact support.';
                    $respondWith['path'] = 'Invalid login. Kindly contact support.';
                    return $this->json($respondWith);
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
                $respondWith['path'] = $result['path'];
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
     * @Route("/registerAction", name="Shared registerAction")
     */
    public function register_Action()
    {
        $OpsService = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $username = trim($_POST['username']);
        $work_email = trim($_POST['email']);
        $mobile_number = trim($_POST['mobile_number']);
        $ps1 = trim($_POST['loginpassword']);
        $uppercase = preg_match('@[A-Z]@', $ps1);
        $lowercase = preg_match('@[a-z]@', $ps1);
        $number = preg_match('@[0-9]@', $ps1);
        $specialChars = preg_match('@[^\w]@', $ps1);
        if ($username == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your preferred User Name.";
            return $this->json($respondWith);
        } else if ($work_email == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Work Email Address.";
            return $this->json($respondWith);
        } else if ($ps1 == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Password.";
            return $this->json($respondWith);
        } else {
            $password = hash('ripemd160', $ps1);
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $pre_saved = $repository->confirm_not_already_registered($work_email);
            if (empty($pre_saved)) {
                $last_id = 0;
                $reset_code = $OpsService->generate_random_string($length = 6);
                $user_info = array(
                    'user_name' => $username,
                    'user_email' => $work_email,
                    'password' => $password,
                    'mobile_number' => $mobile_number,
                    'OTP' => $reset_code,
                );
                $user_info = new Clients;
                $user_info->setUserName($username);
                $user_info->setEmailAddress($work_email);
                $user_info->setPhone($mobile_number);
                $user_info->setPassword($password);
                $user_info->setTempOtp($reset_code);
                $user_info->setDateOfJoining(new \Datetime());
                $user_info->setModifiedDate(new \Datetime());
                $em->persist($user_info);
                $em->flush();
                $last_id = $user_info->getRecordId();
                $reset_code = $OpsService->generate_random_string($length = 6);
                $save_data = array(
                    "work_email" => $work_email,
                    "ps1" => $password,
                    'OTP' => $reset_code,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Clients::class);
                $user_id = $repository->update_user_password($save_data);
                //$saved = 1;
                //$last_id = $last_id;
                if ($last_id > 0) {
                    $html_msg = $this->renderView('emails/email-otp.html.twig', [
                        'title' => "Document Render",
                        'client_name' => $username,
                        'otp' => $reset_code,
                    ]);
                    $message = $OpsService->send_email_reg_otp($last_id, $username, $work_email, $html_msg);
                    $obsfcate_email = preg_replace("/(?!^).(?=[^@]+@)/", "*", $work_email);
                    $respondWith['status'] = 'ok';
                    $respondWith['messages'] = "Hello " . $username . ". Please enter the OTP we sent to your email " . $obsfcate_email . ".";
                    return $this->json($respondWith);
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "Something went wrong. Please try again. If the issue persists, kindly contact support.";
                    return $this->json($respondWith);
                    //exit;
                }
                // if ($last_id > 0) {
                //     $obsfcate_email = preg_replace("/(?!^).(?=[^@]+@)/", "*", $work_email);
                //     $respondWith['status'] = 'ok';
                //     $respondWith['messages'] = "Welcome " . $username . ". Kindly login.";
                //     return $this->json($respondWith);
                // } else {
                //     $respondWith['status'] = 'fail';
                //     $respondWith['messages'] = "Something went wrong. Please try again.";
                //     return $this->json($respondWith);
                // }
            } else {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "We already have your account. Kindly login.";
                return $this->json($respondWith);
            }
        }
    }

    /**
     * @Route("/resetpsaction-client", name="Shared reset ps action client")
     */
    public function reset_ps_action()
    {
        $OpsService = new OpsService;
        $workemail = trim($_POST['email']);
        $ps1 = trim($_POST['password']);

        $uppercase = preg_match('@[A-Z]@', $ps1);
        $lowercase = preg_match('@[a-z]@', $ps1);
        $number = preg_match('@[0-9]@', $ps1);
        $specialChars = preg_match('@[^\w]@', $ps1);
        if ($workemail == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Email Address.";
            return $this->json($respondWith);
            //exit;
        } else if ($ps1 == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Password.";
            return $this->json($respondWith);
            //exit;
        } else if (!$uppercase || !$lowercase || !$number || strlen($ps1) < 5) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = 'Error';
            $respondWith['messages'] = "Password should be at least 5 characters long and should include at least one upper case letter or one number. You can use one or more special character.";
            return $this->json($respondWith);
            //exit;
        } else {
            $password = hash('ripemd160', $ps1);
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $validity = $repository->verify_registration_reset($workemail);
            $user_info = array();
            if (empty($validity)) {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "Sorry, Action not allowed for this email address.";
                return $this->json($respondWith);
            } else {
                $reset_code = $OpsService->generate_random_string($length = 6);
                $save_data = array(
                    "work_email" => $workemail,
                    "ps1" => $password,
                    'OTP' => $reset_code,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Clients::class);
                $user_id = $repository->update_user_password($save_data);
                $saved = 1;
                $last_id = $saved;
                if ($saved > 0) {
                    $html_msg = $this->renderView('emails/email-otp.html.twig', [
                        'title' => "Document Render",
                        'client_name' => $validity[0]['user_name'],
                        'otp' => $reset_code,
                    ]);
                    $message = $OpsService->send_email_reg_otp($last_id, $validity[0]['user_name'], $workemail, $html_msg);
                    $obsfcate_email = preg_replace("/(?!^).(?=[^@]+@)/", "*", $workemail);
                    $respondWith['status'] = 'ok';
                    $respondWith['messages'] = "Hello " . $validity[0]['user_name'] . ". Please enter the OTP we sent to your email " . $obsfcate_email . ".";
                    return $this->json($respondWith);
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "We will sent reset password instructions to your email if you have registered an account with us.";
                    return $this->json($respondWith);
                    //exit;
                }
            }
        }
    }

    /**
     * @Route("/activate-otp-client", name="Shared activate-otp-client")
     */
    public function activate_otp()
    {
        $OpsService = new OpsService;
        $ver_otp = trim($_POST['ver_otp']);
        $assocemail = trim($_POST['assocemail']);
        if ($assocemail == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Error occurred. Please contact support.";
            return $this->json($respondWith);
        } else if ($ver_otp == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter the OTP we sent to your email.";
            return $this->json($respondWith);
        } else {
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $set_otp = $repository->get_acc_otp($assocemail);

            if (empty($set_otp)) {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "OTP purged. Please contact support.";
                return $this->json($respondWith);
            }

            $otp_time = $set_otp[0]['otp_time'];
            $time_now = strtotime("now");
            //$otp_time = strtotime($otp_time);

            $datetime1 = new \DateTime(); //start time
            $datetime2 = new \DateTime($otp_time); //end time

            // var_dump($datetime1);
            // var_dump($datetime2);

            $interval = $datetime1->diff($datetime2);
            //var_dump($interval);
            $interval_val = $interval->format('%H');
            // var_dump($interval_val);
            // exit;
            if ($interval_val >= 24) {

                // var_dump($interval);
                // exit;
                // $time_difference = $time_now - $otp_time;
                // $interval = $time_now->diff($otp_time);
                //  var_dump($time_difference);
                //  exit;
                // if ((abs($time_difference) / 3600) > 60) {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "OTP purged. You took too long. Kindly reload and reset password again.";
                return $this->json($respondWith);
            }

            if ($set_otp[0]['temp_otp'] == '') {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "OTP purged. Please contact support.";
                return $this->json($respondWith);
            }
            if ($ver_otp == $set_otp[0]['temp_otp']) {
                $repository = $this->getDoctrine()
                    ->getRepository(Clients::class);
                $repository->activate_acc_otp($assocemail);

                $respondWith['status'] = 'ok';
                $respondWith['messages'] = "You have activated your account! You can now log in.";
                return $this->json($respondWith);
            } else {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "You have entered a wrong OTP.";
                return $this->json($respondWith);
            }
        }
    }

    /**
     * @Route("/logout", name="log user out")
     */
    public function logout()
    {
        $this->session->remove('token');
        $respondWith['status'] = 'ok';
        $respondWith['messages'] = "Logged out";
        $respondWith['sendto'] = 1;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-services", name="get-services")
     */
    public function get_services()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_all_services();
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
     * @Route("/get-trainer-services", name="get-trainer-services")
     */
    public function get_trainer_services()
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
        if (isset($_POST['trainer'])) {
            $trainer_id = $_POST['trainer'];
        } else if (isset($_GET['trainer'])) {
            $trainer_id = $_GET['trainer'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some trainer errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_trainer_services($trainer_id);
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
     * @Route("/get-trainer-services-morph", name="get-trainer-services-morph")
     */
    public function get_trainer_services_morph()
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
        if (isset($_POST['trainer'])) {
            $trainer_id = $_POST['trainer'];
        } else if (isset($_GET['trainer'])) {
            $trainer_id = $_GET['trainer'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some trainer errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $trainer_id_array = $repository->get_trainer_by_id($trainer_id);
        if (empty($trainer_id_array)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            return $this->json($respondWith);
        }
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_trainer_services_morph($trainer_id_array[0]['client_look_up_id']);
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
     * @Route("/get-trainers", name="get-trainers")
     */
    public function get_trainers()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_all_trainers();
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
            $trainer_id = $result['trainer_id'];
            $gender = $result['gender'];
            $pronoun = '';
            $profile_picture = $result['profile_picture'];
            $user_name = $result['user_name'];
            if ($gender == 'Male') {
                $pronoun = 'him';
            } elseif ($gender == 'Female') {
            $pronoun = 'her';
        } else {
            $pronoun = '';
        }
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $trainer_activities = $repository->get_trainer_activities($record_id);
        $trainer_activities_array = array();
        foreach ($trainer_activities as $trainer_activity):
            $service_id = $trainer_activity['service_id'];
            $service_name = $trainer_activity['service_name'];
            $trainer_activities_array['services'][] = array(
                $service_id,
                $service_name,
            );
        endforeach;
        $returnarray['data'][] = array(
            'record_id' => $trainer_id,
            'user_name' => $user_name,
            'service_name' => $service_name,
            'pronoun' => $pronoun,
            'profile_picture' => $profile_picture,
            'trainer_activities_array' => $trainer_activities_array,
        );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-client-all-sessions", name="get-client-all-sessions")
     */
    public function get_client_all_sessions()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_revamp_events_adm_ovr();
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
            $thumbnail = $result['thumbnail'];
            $start_date = date('dS-M Y', strtotime($start_date));
            $end_date = date('dS-M Y', strtotime($end_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $type_of_trainings = $repository->get_all_event_training($record_id);

            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $age_brackets = $repository->get_all_event_age_bracket($record_id);

            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'name' => $session_title,
                'startdate' => $start_date,
                'enddate' => $end_date,
                'location' => $location,
                'unit_ui_display' => $unit_ui_display,
                'dropdown' => $dropdown,
                'thumbnail' => $thumbnail,
                'type_of_trainings' => $type_of_trainings,
                'age_brackets' => $age_brackets,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-client-all-sessions-ongoing", name="get-client-all-sessions-ongoing")
     */
    public function get_client_all_sessions_ongoing()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_revamp_events_adm_ovr_ongoing();
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
            $thumbnail = $result['thumbnail'];
            $start_date = date('dS-M Y', strtotime($start_date));
            $end_date = date('dS-M Y', strtotime($end_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'record_id' => $record_id,
                'name' => $session_title,
                'startdate' => $start_date,
                'enddate' => $end_date,
                'location' => $location,
                'unit_ui_display' => $unit_ui_display,
                'dropdown' => $dropdown,
                'thumbnail' => $thumbnail,
            );
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-top-trainers", name="get-top-trainers")
     */
    public function get_top_trainers()
    {
        sleep(1);
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_top_trainers();
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
            $gender = $result['gender'];
            $pronoun = '';
            $profile_picture = $result['profile_picture'];
            $user_name = $result['user_name'];
            if ($gender == 'Male') {
                $pronoun = 'him';
            } elseif ($gender == 'Female') {
            $pronoun = 'her';
        } else {
            $pronoun = '';
        }
        $returnarray['data'][] = array(
            'record_id' => $record_id,
            'user_name' => $user_name,
            'pronoun' => $pronoun,
            'profile_picture' => $profile_picture,
        );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-trainer-by-id", name="get-trainer-by-id")
     */
    public function get_trainer_by_id()
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
            //exit;
        }
        if (isset($_POST['trainer-id'])) {
            $trainer_id = $_POST['trainer-id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_this_trainer($trainer_id);
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
            $gender = $result['gender'];
            $pronoun = '';
            $profile_picture = $result['profile_picture'];
            $user_name = $result['user_name'];
            if ($gender == 'Male') {
                $pronoun = 'Male';
            } elseif ($gender == 'Female') {
            $pronoun = 'Female';
        } else {
            $pronoun = 'Undisclosed';
        }
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $trainer_activities = $repository->get_trainer_activities($record_id);
        $trainer_activities_array = array();
        foreach ($trainer_activities as $trainer_activity):
            $service_id = $trainer_activity['service_id'];
            $service_name = $trainer_activity['service_name'];
            $trainer_activities_array['services'][] = array(
                $service_id,
                $service_name,
            );
        endforeach;
        $returnarray['data'][] = array(
            'record_id' => $record_id,
            'user_name' => $user_name,
            'service_name' => $service_name,
            'pronoun' => $pronoun,
            'profile_picture' => $profile_picture,
            'trainer_activities_array' => $trainer_activities_array,
        );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-client-sessions", name="get-client-sessions")
     */
    public function get_client_sessions()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $results = $repository->get_my_sessions($userid);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no sessions.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['sess_id'];
            $session_booked_date = $result['booking_date'];
            $session_date = $result['start_date'];
            $session_end__date = $result['end_date'];
            $title = $result['session_title'];
            $status = $result['status'];

            $number_of_sessions = $result['number_of_sessions'];
            $cost = $result['cost'];

            $display_date = date('dS-M Y', strtotime($session_date));
            $display_time = date('h:i A', strtotime($session_date));

            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $session_date = date('dS-M Y', strtotime($session_date));
            $session_end__date = date('dS-M Y', strtotime($session_end__date));

            $dropdown = '';
            $rating_text = '';
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_booked_session_level_params($rating = 5, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $title,
                $session_booked_date,
                $session_date,
                $session_end__date,
                $number_of_sessions,
                $cost,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/book-session", name="book-session")
     */
    public function book_session()
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
        if (isset($_POST['trainer-id'])) {
            $trainer_id = $_POST['trainer-id'];
        } else {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Please reload this page and try again.";
            return $this->json($respondWith);
        }
        $trainer_id = $_POST['trainer-id'];
        $service = $_POST['service'];
        $session_date = $_POST['session_date'];
        $training_activities = $_POST['training_activities'];
        $selected_trainers = $_POST['selected_trainers'];
        if (empty($service)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Kindly select the service";
            return $this->json($respondWith);
        }
        if (empty($session_date)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Kindly select the training date";
            return $this->json($respondWith);
        }
        if (empty($training_activities)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Kindly enter a description of activities your would like to do with your trainer";
            return $this->json($respondWith);
        }
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $trainer_details = $repository->get_this_trainer($trainer_id);
        if (empty($trainer_details)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Fix some errors";
            $respondWith['messages'] = "Invalid trainer profile";
            return $this->json($respondWith);
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $dt_max = date('Y-m-d H:i:s', strtotime($session_date));
        $training_session_data = array(
            'userid' => $userid,
            'trainer_id' => $trainer_details[0]['trainer_profile_id'],
            'service' => $service,
            'session_date' => $dt_max,
            'training_activities' => $training_activities,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $last_id = $repository->save_training_session($training_session_data);

        $selected_trainers = explode(',', $selected_trainers);
        foreach ($selected_trainers as $selected_trainer):
            $repository = $this->getDoctrine()
                ->getRepository(Services::class);
            $repository->save_kid_session($last_id, $selected_trainer);
        endforeach;

        if (empty($last_id)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Not saved.";
            return $this->json($respondWith);
        }
        if ($last_id > 1) {
            $respondWith['status'] = 'ok';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Saved your session.";
            return $this->json($respondWith);
        }
    }

    /**
     * @Route("/get-my-short-schedule", name="get-my-short-schedule")
     */
    public function get_my_short_schedule()
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
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_my_sessions_short($userid);
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no sessions yet.";
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
            $display_time = date('h:i A', strtotime($session_date));
            $dropdown = '';
            $rating_text = '';
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_session_level_params($rating, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                'service_name' => $service_name,
                'display_date' => $display_date,
                'display_time' => $display_time,
                'trainer_name' => $trainer_name,
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
     * @Route("/get-service-count", name="get-allservice-count")
     */
    public function get_service_count()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $results = $repository->get_service_count();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['service_count'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['service_count'] = $results[0]['service_count'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-session-count", name="get-session-count")
     */
    public function get_session_count()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->get_session_count($userid);
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
     * @Route("/get-trainer-count", name="get-trainer-count")
     */
    public function get_trainer_count()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(TrainerProfiles::class);
        $results = $repository->get_trainer_count();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data.";
            $respondWith['trainer_count'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['trainer_count'] = $results[0]['trainer_count'];
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-profile-details", name="get-profile-details")
     */
    public function get_profile_details()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
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
        $client_age = floor((time() - strtotime($results[0]['date_of_birth'])) / 31556926);
        $results[0]['client_age'] = $client_age;
        $results[0]['member_since'] = date('Y-m-d', strtotime($results[0]['date_of_joining']));
        $results[0]['dob_vw'] = Date('Y-m-d\TH:i', strtotime($results[0]['date_of_birth']));
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $results[0];
        return $this->json($respondWith);
    }

    /**
     * @Route("/update-bio", name="update-bio")
     */
    public function update_bio()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $client_full_name = trim($_POST['client_full_name']);
        $client_phone = trim($_POST['client_phone']);
        $client_location = trim($_POST['client_location']);
        $client_nationality = trim($_POST['client_nationality']);
        $client_dob = trim($_POST['client_dob']);
        $client_twitter = trim($_POST['client_twitter']);
        $client_insta = trim($_POST['client_insta']);
        $client_fb = trim($_POST['client_fb']);
        $client_bio_field = filter_var(trim($_POST['client_bio_field']), FILTER_SANITIZE_STRING);
        if ($client_full_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your full name.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_phone == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your mobile number.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_location == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your location.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_nationality == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your nationality.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_dob == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your date of birth.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_bio_field == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your bio (a small description will do).";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        $client_dob = date('Y-m-d H:i:s', strtotime($client_dob));
        $user_details = array(
            'userid' => $userid,
            'client_full_name' => $client_full_name,
            'client_phone' => $client_phone,
            'client_location' => $client_location,
            'client_nationality' => $client_nationality,
            'client_dob' => $client_dob,
            'client_twitter' => $client_twitter,
            'client_insta' => $client_insta,
            'client_fb' => $client_fb,
            'client_bio_field' => $client_bio_field,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->update_client_name($user_details);
        if (!$update_name_results) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->update_client_profile($user_details);
        if (!$results) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        $respondWith['data'] = $user_details;
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-client-lists", name="get-client-lists")
     */
    public function get_client_lists()
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
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_full_client_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $user_name = $result['user_name'];
            $email_address = $result['email_address'];
            $phone = $result['phone'];
            $is_active = $result['is_active'];
            $date_of_joining = $result['date_of_joining'];
            $date_of_joining = date('dS-M Y', strtotime($date_of_joining));
            $dropdown = '';
            $rating_text = '';
            if ($is_active == 1) {
                $dropdown = '<div class="btn-group">'
                    . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Make Payment</a><br>'
                    . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                    . '</div>'
                    . '</div>';
                $unit_ui_display = '<a href="javascript:;" class="btn btn-sm btn-light btn-block radius-30">Active</a>';
            } elseif ($is_active == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle btn-sm btn-block radius-30" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Change Trainer</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<a href="javascript:;" class="btn btn-sm btn-light btn-block radius-30">In-Active</a>';
        }
        $returnarray['data'][] = array(
            'user_name' => $user_name,
            'email_address' => $email_address,
            'phone' => $phone,
            'date_of_joining' => $date_of_joining,
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
     * @Route("/get-trainer-lists", name="get-trainer-lists")
     */
    public function get_trainer_lists()
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
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_full_trainer_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data yet.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $user_name = $result['user_name'];
            $email_address = $result['email_address'];
            $phone = $result['phone'];
            $is_active = $result['is_active'];
            $date_of_joining = $result['date_of_joining'];
            $date_of_joining = date('dS-M Y', strtotime($date_of_joining));
            $dropdown = '';
            $rating_text = '';
            if ($is_active == 1) {
                $dropdown = '<div class="btn-group">'
                    . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Make Payment</a><br>'
                    . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                    . '</div>'
                    . '</div>';
                $unit_ui_display = '<a href="javascript:;" class="btn btn-sm btn-light btn-block radius-30">Active</a>';
            } elseif ($is_active == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle btn-sm btn-block radius-30" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Change Trainer</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<a href="javascript:;" class="btn btn-sm btn-light btn-block radius-30">In-Active</a>';
        }
        $returnarray['data'][] = array(
            'user_name' => $user_name,
            'email_address' => $email_address,
            'phone' => $phone,
            'date_of_joining' => $date_of_joining,
            'unit_ui_display' => $unit_ui_display,
        );
        endforeach;
        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Loaded";
        $respondWith['messages'] = "Prepared data.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    public function getAge($date)
    {
        $dob = new DateTime($date);
        $now = new DateTime();
        $difference = $now->diff($dob);
        $age = $difference->y;
        return $age;
    }

    /**
     * @Route("/reset-ps1", name="reset-ps1")
     */
    public function reset_ps1()
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
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $results = $repository->get_full_trainer_list();
        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no data yet.";
            return $this->json($respondWith);
        }
        $i = 1;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Loaded";
        $respondWith['messages'] = "Prepared data.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/book_session", name="book_session")
     */
    public function client_book_session()
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
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $record_id = $_POST['recordid'];
        $session_id = $record_id;
        $number_of_kids = $_POST['number_of_kids'];
        $number_of_chaperone = $_POST['number_of_chaperone'];
        $extra_info = $_POST['extra_info'];
        $selected_trainers = $_POST['selected_trainers'];

        if ($number_of_kids == "") {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Kindly enter the number of kids.";
            return $this->json($respondWith);
        }

        if ($number_of_chaperone == '') {
            $number_of_chaperone = 0;
        }

        $booking_details = array(
            'userid' => $userid,
            'record_id' => $record_id,
            'number_of_kids' => $number_of_kids,
            'number_of_chaperone' => $number_of_chaperone,
            'extra_info' => $extra_info,
            'is_school_booking' => 0,
        );

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $is_saved = $repository->check_pre_saved_event($booking_details);

        if (!empty($is_saved)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have already made a booking for this event.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $is_saved = $repository->check_pre_saved_event($booking_details);

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $event_max_children = $repository->get_event_max_children($booking_details);

        if (empty($event_max_children)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Not saved. Please try again later";
            return $this->json($respondWith);
        }

        if ($number_of_kids > $event_max_children[0]['max_attendee'] && $event_max_children[0]['max_attendee'] > 0) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have entered more than " . $event_max_children[0]['max_attendee'] . " children with is the maximum attendance.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $booking_so_far_children = $repository->get_event_booking_so_far($booking_details);

        $remaining_slots = $event_max_children[0]['max_attendee'] - $booking_so_far_children[0]['number_of_children_so_far'];

        if ($number_of_kids > $remaining_slots && $event_max_children[0]['max_attendee'] > 0) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Sorry, we only have " . $remaining_slots . " slots left. Kindly checkout our other ongoing and upcoming events.";
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(EventBookings::class);
        $event_max_children = $repository->save_new_event_booking($booking_details);

        //$event_max_children[0]['sdfsd'];
        //$selected_trainers = explode(',', $selected_trainers);
        //var_dump($selected_trainers);
        foreach ($selected_trainers as $selected_trainer):
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $repository->save_kid_session($session_id, $selected_trainer);
        endforeach;

        $i = 1;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Loaded";
        $respondWith['messages'] = "Saved. Redirecting to payment gateway.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/opp_apply", name="opp_apply")
     */
    public function opp_apply()
    {
        $sescontrol = new SessionController;
        $returnarray = array();
        $userid = '';
        $token = 0;
        //$userid_array = $sescontrol->getUserid($token);
        $userid = 0;

        $title = $_POST['title'];
        $names = $_POST['names'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        $is_studying = 0;
        $is_comp_studying = 0;
        $is_diff_able = 0;
        $yom_completion = 0;

        if (isset($_POST['is_studying'])) {
            if ($_POST['is_studying'] == 'on') {
                $is_studying = 1;
            }
        }
        if (isset($_POST['is_comp_studying'])) {
            if ($_POST['is_comp_studying'] == 'on') {
                $is_comp_studying = 1;
            }
        }
        if (isset($_POST['is_diff_able'])) {
            if ($_POST['is_diff_able'] == 'on') {
                $is_diff_able = 1;
            }
        }
        if (isset($_POST['yom_completion'])) {
            $yom_completion = $_POST['yom_completion'];
        }

        $institution_name = $_POST['institution_name'];
        $program = $_POST['program'];
        $certification = $_POST['certification'];
        $certification_level = $_POST['certification_level'];
        $employer_name = $_POST['employer_name'];
        $employment_position = $_POST['employment_position'];
        $reason_for_leaving_employment = $_POST['reason_for_leaving_employment'];
        $volunteer_experience = $_POST['volunteer-experience'];
        $activity_coached = $_POST['activity_coached'];
        $age_group_coached = $_POST['age_group_coached'];
        $prefered_age_group = $_POST['prefered-age-group'];
        $condition_coached_diff_able = $_POST['condition_coached_diff_able'];
        $activity_coached_diff_able = $_POST['activity_coached_diff_able'];
        $coaching_philosophy = $_POST['coaching-philosophy'];
        $availability = $_POST['availability'];
        $declaration = $_POST['declaration'];
        $yom_completion = $_POST['yom_completion'];

        if ($title == "") {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Kindly enter your title.";
            return $this->json($respondWith);
        }

        if ($names == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Kindly enter your name.";
            return $this->json($respondWith);
        }

        if ($phone == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Kindly enter your phone.";
            return $this->json($respondWith);
        }

        if ($phone == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Kindly enter your email.";
            return $this->json($respondWith);
        }

        $booking_details = array(
            'title' => $title,
            'names' => $names,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'volunteer_experience' => $volunteer_experience,

            'prefered_age_group' => $prefered_age_group,
            'coaching_philosophy' => $coaching_philosophy,
            'availability' => $availability,
            'declaration' => $declaration,

            'is_studying' => $is_studying,
            'is_comp_studying' => $is_comp_studying,
            'is_diff_able' => $is_diff_able,

        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $is_saved = $repository->save_new_opp_booking($booking_details);

        if (empty($is_saved)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Not saved. Please try again later";
            return $this->json($respondWith);
        }
        $i = 1;
        //institutions
        $institution_index = 0;
        $yoe_index = 0;
        foreach ($institution_name as $ins_name) {
            if (trim($ins_name) !== '') {
                $program = '';
                $yom = '';
                if (isset($program[$institution_index])) {
                    $program = $program[$institution_index];
                }
                if (isset($yom_completion[$institution_index])) {
                    $yom = $yom_completion[$institution_index];
                }
                $sv_data = array(
                    'record_id' => $is_saved,
                    'ins_name' => $ins_name,
                    'program' => $program,
                    'yom' => $yom,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_opp_institutions($sv_data);
            }
            $institution_index++;
            $yoe_index++;
        }

        $certification_index = 0;
        foreach ($certification as $certification_lev) {
            if (trim($certification_lev) !== '') {
                $cert = '';
                if (isset($certification_level[$certification_index])) {
                    $cert = $certification_level[$certification_index];
                }
                $sv_data = array(
                    'record_id' => $is_saved,
                    'certification_lev' => $certification_lev,
                    'cert' => $cert,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_opp_cert($sv_data);
            }
            $certification_index++;
        }

        $employer_index = 0;
        foreach ($employer_name as $employer_name_lex) {
            if (trim($employer_name_lex) !== '') {
                $empp = '';
                $emp_l_r = '';
                if (isset($employment_position[$employer_index])) {
                    $empp = $employment_position[$employer_index];
                }
                if (isset($reason_for_leaving_employment[$employer_index])) {
                    $emp_l_r = $reason_for_leaving_employment[$employer_index];
                }
                $sv_data = array(
                    'record_id' => $is_saved,
                    'employer_name_lex' => $certification_lev,
                    'empp' => $empp,
                    'emp_l_r' => $emp_l_r,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_opp_emplyer($sv_data);
            }
            $employer_index++;
        }

        $age_index = 0;
        foreach ($activity_coached as $activity_coached_lex) {
            if (trim($activity_coached_lex) !== '') {
                $ag_Val = '';

                if (isset($age_group_coached[$age_index])) {
                    $ag_Val = $age_group_coached[$age_index];
                }
                $sv_data = array(
                    'record_id' => $is_saved,
                    'activity_coached' => $activity_coached_lex,
                    'age_val' => $ag_Val,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_opp_activities($sv_data);
            }
            $age_index++;
        }

        $able_index = 0;
        foreach ($condition_coached_diff_able as $condition_coached_diff_able_lex) {
            if (trim($condition_coached_diff_able_lex) !== '') {
                $act_Val = '';

                if (isset($condition_coached_diff_able_lex[$able_index])) {
                    $act_Val = $condition_coached_diff_able_lex[$able_index];
                }
                $sv_data = array(
                    'record_id' => $is_saved,
                    'condition_coached' => $condition_coached_diff_able_lex,
                    'act_Val' => $act_Val,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_condition_activities($sv_data);
            }
            $able_index++;
        }

        // /$availability

        foreach ($availability as $availability_x) {
            if (trim($availability_x) !== '') {

                $sv_data = array(
                    'record_id' => $is_saved,
                    'condition_coached' => $availability_x,
                );
                $repository = $this->getDoctrine()
                    ->getRepository(Blog::class);
                $inst_id = $repository->save_new_condition_availability($sv_data);
            }
            $able_index++;
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Loaded";
        $respondWith['messages'] = "Thank you for your time.";
        $respondWith['data'] = $returnarray;
        return $this->json($respondWith);
    }

    /**
     * @Route("/client-kid-status-update", name="client-kid-status-update")
     */
    public function client_kid_status_update()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $client_full_name = trim($_POST['client_full_name']);
        $client_dob = trim($_POST['client_dob']);
        $client_allergies = filter_var(trim($_POST['client_allergies']), FILTER_SANITIZE_STRING);
        $client_medical_conditions = filter_var(trim($_POST['client_medical_conditions']), FILTER_SANITIZE_STRING);

        $client_special_needs = filter_var(trim($_POST['client_special_needs']), FILTER_SANITIZE_STRING);
        $client_behavioral_conditions = filter_var(trim($_POST['client_behavioral_conditions']), FILTER_SANITIZE_STRING);
        $selected_conditions = $_POST['selected_conditions'];
        if ($client_full_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your full name.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_dob == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your date of birth.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        $client_dob = date('Y-m-d H:i:s', strtotime($client_dob));
        $user_details = array(
            'userid' => $userid,
            'client_full_name' => $client_full_name,
            'client_dob' => $client_dob,
            'client_allergies' => $client_allergies,
            'client_medical_conditions' => $client_medical_conditions,

            'client_special_needs' => $client_special_needs,
            'client_behavioral_conditions' => $client_behavioral_conditions,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->create_kid_details($user_details);
        if ($update_name_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        //var_dump($selected_conditions);
        //$selected_trainers = explode(',', $selected_conditions);
        foreach ($selected_conditions as $selected_trainer):
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $repository->create_kid_needs($update_name_results, $selected_trainer);
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/create-chaperone", name="create-chaperone")
     */
    public function client_kid_create_chaperone()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $client_full_name = trim($_POST['client_full_name']);
        $relationship = trim($_POST['relationship']);
        $phonenumber = filter_var(trim($_POST['phonenumber']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
        $location = trim($_POST['location']);

        if ($client_full_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your full name.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($relationship == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter chaperone relationship to you.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($phonenumber == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter phone number.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($email == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter email.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($location == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter location.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        //$client_dob = date('Y-m-d H:i:s', strtotime($client_dob));
        $user_details = array(
            'userid' => $userid,
            'client_full_name' => $client_full_name,
            'relationship' => $relationship,
            'phonenumber' => $phonenumber,
            'email' => $email,
            'location' => $location,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->create_kid_chaperone($user_details);
        if ($update_name_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        //var_dump($selected_conditions);
        //$selected_trainers = explode(',', $selected_conditions);

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/client-kid-status-update-revamp", name="client-kid-status-update-revamp")
     */
    public function client_kid_status_update_revamp()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $record_id = trim($_POST['record_id']);
        $client_full_name = trim($_POST['client_full_name']);
        $client_dob = trim($_POST['client_dob']);
        $client_allergies = filter_var(trim($_POST['client_allergies']), FILTER_SANITIZE_STRING);
        $client_medical_conditions = filter_var(trim($_POST['client_medical_conditions']), FILTER_SANITIZE_STRING);

        $client_special_needs = filter_var(trim($_POST['client_special_needs']), FILTER_SANITIZE_STRING);
        $client_behavioral_conditions = filter_var(trim($_POST['client_behavioral_conditions']), FILTER_SANITIZE_STRING);
        $selected_conditions = $_POST['selected_conditions'];
        if ($client_full_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your full name.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Invalid record, kindly reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($client_dob == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your date of birth.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        $client_dob = date('Y-m-d H:i:s', strtotime($client_dob));
        $user_details = array(
            'record_id' => $record_id,
            'userid' => $userid,
            'client_full_name' => $client_full_name,
            'client_dob' => $client_dob,
            'client_allergies' => $client_allergies,
            'client_medical_conditions' => $client_medical_conditions,
            'client_special_needs' => $client_special_needs,
            'client_behavioral_conditions' => $client_behavioral_conditions,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->update_kid_details($user_details);
        if ($update_name_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $repository->clear_kid_need($record_id);

        foreach ($selected_conditions as $selected_trainer):
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $repository->create_kid_needs($record_id, $selected_trainer);
        endforeach;

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/client-chaperone-status-update-revamp", name="client-chaperone-status-update-revamp")
     */
    public function client_chaperone_status_update_revamp()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];
        $record_id = trim($_POST['record_id']);
        $client_full_name = trim($_POST['client_full_name']);
        $relationship = trim($_POST['relationship']);
        $phonenumber = filter_var(trim($_POST['phonenumber']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
        $location = filter_var(trim($_POST['location']), FILTER_SANITIZE_STRING);

        if ($client_full_name == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter your full name.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($record_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Invalid record, kindly reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($relationship == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter relationship.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($phonenumber == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter phonenumber.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($email == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter email.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($location == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please enter location.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        //$client_dob = date('Y-m-d H:i:s', strtotime($client_dob));
        $user_details = array(
            'record_id' => $record_id,
            'userid' => $userid,
            'client_full_name' => $client_full_name,
            'relationship' => $relationship,
            'phonenumber' => $phonenumber,
            'email' => $email,
            'location' => $location,
        );
        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->update_chaperone_details($user_details);
        if ($update_name_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "null";
        $respondWith['messages'] = "Done.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/client-fetch-my-kids", name="client-fetch-my-kids")
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
            ->getRepository(Clients::class);
        $results = $repository->get_kid_details($userid);

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
            $member_id = $result['member_id'];
            $kidsname = $result['kidsname'];
            $date_of_birth = $result['date_of_birth'];
            $status = $result['status'];
            $created_date = $result['created_date'];

            $age = (date('Y') - date('Y', strtotime($date_of_birth)));
            $display_date = date('dS-M Y', strtotime($created_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_kids_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $kidsname,
                $age,
                //$display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-fetch-my-kids-adm", name="client-fetch-my-kids-adm")
     */
    public function get_all_new_sessions_admx_adm()
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
        $results = $repository->get_full_kid_details($userid);

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
            $member_id = $result['member_id'];
            $user_name = $result['user_name'];
            $parent = "#" . $member_id . "-" . $user_name;
            $kidsname = $result['kidsname'];
            $date_of_birth = $result['date_of_birth'];
            $status = $result['status'];
            $created_date = $result['created_date'];

            $age = (date('Y') - date('Y', strtotime($date_of_birth)));
            $display_date = date('dS-M Y', strtotime($created_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_kids_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $parent,
                $kidsname,
                $age,
                //$display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-fetch-gallery-list", name="client-fetch-gallery-list")
     */
    public function client_fetch_gallery_list()
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
        $results = $repository->get_all_gallery_images($userid);

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
            $image_url = $result['image_url'];
            $description = $result['description'];
            $on_date = $result['on_date'];
            $status = $result['status'];

            //$on_date = (date('Y') - date('Y', strtotime($on_date)));
            $on_date = date('dS-M Y', strtotime($on_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_gallery_params($record_id, $status, $image_url);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $description,
                $on_date,
                //$display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-fetch-my-chaperone", name="client-fetch-my-chaperone")
     */
    public function get_all_new_chaperone_admx()
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
        $results = $repository->get_chaperone_details($userid);

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
            $member_id = $result['member_id'];
            $name = $result['name'];
            $relationship = $result['relationship'];
            $phonenumber = $result['phonenumber'];
            $email = $result['email'];
            $location = $result['location'];
            $location = $result['location'];
            $status = $result['status'];
            $created_date = $result['on_date'];

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_chaperone_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $name,
                //$age,
                //$display_date,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-update-kid-status", name="client-update-kid-status")
     */
    public function client_update_kid_status()
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
            ->getRepository(Clients::class);
        $results = $repository->update_kid_status($data_t_save);
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
     * @Route("/client-update-gallery-status", name="client-update-gallery-status")
     */
    public function client_update_gallery_status()
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
        $results = $repository->update_gallery_status($data_t_save);
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
     * @Route("/client-update-chaperone-status", name="client-update-chaperone-status")
     */
    public function client_update_chaperone_status()
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
            ->getRepository(Clients::class);
        $results = $repository->update_chaperone_status($data_t_save);
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
     * @Route("/client-update-booking-status", name="client-update-booking-status")
     */
    public function client_update_booking_status()
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
        $results = $repository->update_session_data($data_t_save);
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
     * @Route("/get-my-active-kids-list", name="get-my-active-kids-list")
     */
    public function get_my_active_kids_list()
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
            ->getRepository(Clients::class);
        $results = $repository->get_active_kid_details($userid);

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
     * @Route("/get-conditions-list", name="get-conditions-list")
     */
    public function get_conditions_list()
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
            ->getRepository(Clients::class);
        $results = $repository->get_active_condition_list($userid);

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
     * @Route("/get-my-active-event-kids-list", name="get-my-active-event-kids-list")
     */
    public function get_my_event_active_kids_list()
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
        $record_id = $_GET['record_id'];
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_view_event_kids_booking($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $child_id = $result['child_id'];
            $kidsname = $result['kidsname'];
            $date_of_birth = $result['date_of_birth'];
            $age = (date('Y') - date('Y', strtotime($date_of_birth)));
            $status = 1;
            $repository = $this->getDoctrine()
                ->getRepository(SessionsRevamp::class);
            $session_count = $repository->get_kids_session_count($record_id, $child_id);

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_event_kids_params($child_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $kidsname,
                $age,
                $session_count[0]['session_count'],
                //$unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-api-event-trainer-list", name="get-api-event-trainer-list")
     */
    public function get_api_event_trainer_list()
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
        $record_id = $_GET['record_id'];
        $userid = $userid_array['userId'];

        $repository = $this->getDoctrine()
            ->getRepository(SessionsRevamp::class);
        $results = $repository->get_view_event_trainer_list($record_id);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "No data found.";
            return $this->json($respondWith);
        }

        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $trainer_id = $result['trainer_id'];
            $user_name = $result['user_name'];
            $trainer_verification_status = $result['traiver_verification_status'];

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_trainer_events_params($record_id, $trainer_verification_status, $trainer_id);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray['data'][] = array(
                $user_name,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/save-rating", name="save-rating")
     */
    public function save_rating()
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
            //exit;
        }
        $userid_array = $sescontrol->getUserid($token);
        $userid = $userid_array['userId'];

        $recordid = trim($_POST['recordid']);
        $stars = trim($_POST['stars']);
        $trainer_id = trim($_POST['trainer_id']);
        $extra_info = trim($_POST['extra_info']);

        if ($recordid == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($trainer_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($stars == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Please select at least one star.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $user_details = array(
            'userid' => $userid,
            'trainer_id' => $trainer_id,
            'recordid' => $recordid,
            'stars' => $stars,
            'extra_info' => $stars,
        );

        // $repository = $this->getDoctrine()
        //     ->getRepository(Clients::class);
        // $update_name_results = $repository->check_if_exist_trainer_rating($user_details);

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $update_name_results = $repository->save_trainer_rating($user_details);

        if ($update_name_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Save failed.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Saved.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/save-message", name="save-message")
     */
    public function save_message()
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

        $recordid = trim($_POST['recordid']);
        $trainer_id = trim($_POST['trainer_id']);
        $extra_info = trim($_POST['extra_info']);

        if ($recordid == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Record error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($trainer_id == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Trainer error. Please reload page.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }
        if ($extra_info == '') {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "Extra info error. Kindly enter the message.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $message_details = array(
            'userid' => $userid,
            'trainer_id' => $trainer_id,
            'recordid' => $recordid,
            'extra_info' => $extra_info,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Clients::class);
        $save_message_results = $repository->save_chat_message($message_details);

        if ($save_message_results < 1) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "Failed";
            $respondWith['messages'] = "Message not sent.";
            $respondWith['data'] = 0;
            return $this->json($respondWith);
        }

        $respondWith['status'] = 'ok';
        $respondWith['title'] = "Done";
        $respondWith['messages'] = "Message sent.";
        return $this->json($respondWith);
    }

    /**
     * @Route("/get-chats-with-trainers", name="get-chats-with-trainers")
     */
    public function trainer_api_get_chats_with_trainers()
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
            ->getRepository(TrainerProfiles::class);
        $trainer_profile = $repository->get_chats_with($userid);

        $repository = $this->getDoctrine()
            ->getRepository(TrainingSessions::class);
        $results = $repository->trainer_get_my_sessions($trainer_profile[0]['record_id']);

        if (empty($results)) {
            $respondWith['status'] = 'false';
            $respondWith['title'] = "null";
            $respondWith['messages'] = "You have no sessions.";
            return $this->json($respondWith);
        }
        $i = 1;
        $pendingString = '';
        foreach ($results as $result):
            $record_id = $result['record_id'];
            $session_booked_date = $result['start_date'];
            $session_date = $result['end_date'];
            $title = $result['session_title'];
            $status = $result['status'];
            $number_of_sessions = $result['number_of_sessions'];

            $unit_ui_display = '';
            // $repository = $this->getDoctrine()
            //     ->getRepository(TrainerProfiles::class);
            // $trainer_details = $repository->get_this_trainer($trainer_id);
            // $trainer_name = $trainer_details[0]['user_name'];
            $display_date = date('dS-M Y', strtotime($session_date));
            $session_booked_date = date('dS-M Y', strtotime($session_booked_date));
            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_trainer_session_level_params($rating = 0, $record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $returnarray['data'][] = array(
                $title,
                $session_booked_date,
                $display_date,
                $number_of_sessions,
                $unit_ui_display,
                $dropdown,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-kid-full-details", name="get-kid-full-detailst")
     */
    public function get_kid_full_details()
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
            ->getRepository(Clients::class);
        $results = $repository->get_kid_fl_details($attr_id);

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
            $member_id = $result['member_id'];
            $kidsname = $result['kidsname'];
            $date_of_birth = $result['date_of_birth'];

            $allergies = $result['allergies'];
            $medical_conditions = $result['medical_conditions'];
            $special_needs = $result['special_needs'];
            $behavioral_conditions = $result['behavioral_conditions'];

            $status = $result['status'];
            $created_date = $result['created_date'];

            $age = (date('Y') - date('Y', strtotime($date_of_birth)));
            $display_date = date('dS-M Y', strtotime($created_date));
            $date_of_birth = date('Y-m-d\TH:i', strtotime($date_of_birth));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_kids_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $special_need_list = $repository->get_kid_fl_special_needs_details($attr_id);

            $returnarray = array(
                'status' => 'ok',
                'record_id' => $record_id,
                'kidsname' => $kidsname,
                'age' => $age,
                'display_date' => $display_date,
                'date_of_birth' => $date_of_birth,

                'allergies' => $allergies,
                'medical_conditions' => $medical_conditions,
                'special_needs' => $special_needs,
                'behavioral_conditions' => $behavioral_conditions,

                'special_need_list' => $special_need_list,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-chaperone-full-details", name="get-chaperone-full-detailst")
     */
    public function get_chaperone_full_details_ovr()
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
            ->getRepository(Clients::class);
        $results = $repository->get_chap_fl_detailxs($attr_id);

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
            $member_id = $result['member_id'];
            $name = $result['name'];
            $relationship = $result['relationship'];

            $phonenumber = $result['phonenumber'];
            $email = $result['email'];
            $location = $result['location'];

            $returnarray = array(
                'status' => 'ok',
                'record_id' => $record_id,
                'name' => $name,
                'relationship' => $relationship,
                'phonenumber' => $phonenumber,
                'email' => $email,

                'location' => $location,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/get-chaperone-full-details", name="get-chaperone-full-details")
     */
    public function get_chaperone_full_details()
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
            ->getRepository(Clients::class);
        $results = $repository->get_chaperone_fl_details($attr_id);

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
            $name = $result['name'];
            $relationship = $result['relationship'];
            $phonenumber = $result['phonenumber'];

            $email = $result['email'];
            $location = $result['location'];
            $created_date = $result['on_date'];
            $status = $result['status'];

            $display_date = date('dS-M Y', strtotime($created_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_kids_params($record_id, $status);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];

            $returnarray = array(
                'status' => 'ok',
                'record_id' => $record_id,
                'name' => $name,
                'relationship' => $relationship,
                'phonenumber' => $phonenumber,
                'email' => $email,
                'location' => $location,
                'display_date' => $display_date,
            );
        endforeach;
        return $this->json($returnarray);
    }

    /**
     * @Route("/client-contact-us", name="Shared client-contact-us")
     */
    public function client_contact_us()
    {
        sleep(2);
        //$sescontrol = new SessionController;
        $contact_name = trim($_POST['contact_name']);
        $contact_email = trim($_POST['contact_email']);
        $contact_phone = trim($_POST['contact_phone']);
        $contact_subject = trim($_POST['contact_subject']);
        $contact_message = trim($_POST['contact_message']);

        if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your valid email address.";
            return $this->json($respondWith);
            exit;
        } else if ($contact_name == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter full name.";
            return $this->json($respondWith);
            exit;
        } else if ($contact_phone == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your mobile number.";
            return $this->json($respondWith);
            exit;
        } else if ($contact_subject == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter subject.";
            return $this->json($respondWith);
            exit;
        } else if ($contact_message == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your message.";
            return $this->json($respondWith);
            exit;
        }

        $post_data = array(
            'contact_name' => $contact_name,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
            'contact_subject' => $contact_subject,
            'contact_message' => $contact_message,
        );

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $validity = $repository->save_contact_us($post_data);

        if ($validity > 1) {
            $respondWith['status'] = 'ok';
            $respondWith['messages'] = 'Message received. We will revert back ASAP.';
            return $this->json($respondWith);
        } else {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = 'Something went wrong, please try again later.';
            return $this->json($respondWith);
        }
        //}
    }

}
