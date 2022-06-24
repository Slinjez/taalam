<?php
namespace App\Controller;

use App\Entity\Clients;
use App\Entity\TrainerProfiles;
use App\Entity\TrainingSessions;
use App\Service\OpsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use TheSeer\Tokenizer\Exception;

class TrainerSideController extends AbstractController
{
    private $session;
    protected $projectDir;
    public function __construct(SessionInterface $session, KernelInterface $kernel)
    {
        $this->session = $session;
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     * @Route("/loginAction-tr", name="Shared loginAction-tr")
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
            exit;
        } else if ($password == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your password.";
            return $this->json($respondWith);
            exit;
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
                // exit;
                if ($role == 1) {
                    $path = '/client-dash';
                } else if ($role == 2) {
                    $path = '/trainer-dash';
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
     * @Route("/registerAction-tr", name="Shared registerAction-tr")
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
                $user_info->setDateOfJoining(null);
                $user_info->setModifiedDate(null);
                $em->persist($user_info);
                $em->flush();
                $last_id = $user_info->getRecordId();

                // $repository = $this->getDoctrine()
                //     ->getRepository(Clients::class);
                // $last_id = $repository->add_login_info($user_info);

                // var_dump($last_id);
                // exit;
                if ($last_id > 0) {
                    //$obsfcate_email = preg_replace("/(?!^).(?=[^@]+@)/", "*", $work_email);
                    $respondWith['status'] = 'ok';
                    $respondWith['messages'] = "Welcome " . $username . ". Kindly login.";
                    return $this->json($respondWith);
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "Something went wrong. Please try again.";
                    return $this->json($respondWith);
                }
            } else {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "We already have your account. Kindly login.";
                return $this->json($respondWith);
            }
        }
    }

    /**
     * @Route("/resetpsaction-tr", name="Shared resetpsaction-tr")
     */
    public function reset_ps_action()
    {
        $OpsService = new OpsService;
        $workemail = trim($_POST['reset_email_btn']);
        $ps1 = trim($_POST['ps1']);
        $ps2 = trim($_POST['ps2']);
        $uppercase = preg_match('@[A-Z]@', $ps1);
        $lowercase = preg_match('@[a-z]@', $ps1);
        $number = preg_match('@[0-9]@', $ps1);
        $specialChars = preg_match('@[^\w]@', $ps1);
        if ($workemail == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Work Email Addres.";
            return $this->json($respondWith);
            exit;
        } else if ($ps1 == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your Password.";
            return $this->json($respondWith);
            exit;
        } else if (!$uppercase || !$lowercase || !$number || strlen($ps1) < 5) {
            $respondWith['status'] = 'fail';
            $respondWith['title'] = 'Error';
            $respondWith['messages'] = "Password should be at least 5 characters long and should include at least one upper case letter or one number. You can use one or more special character.";
            return $this->json($respondWith);
            exit;
        } else if ($ps1 != $ps2) {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Your passwords mis-match.";
            return $this->json($respondWith);
            exit;
        } else {
            $password = hash('ripemd160', $ps1);
            $repository = $this->getDoctrine()
                ->getRepository(Users::class);
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
                    ->getRepository(Users::class);
                $user_id = $repository->update_user_password($save_data);
                $saved = 1;
                $last_id = $saved;
                if ($saved > 0) {
                    $html_msg = $this->renderView('emails/email-otp.html.twig', [
                        'title' => "Document Render",
                        'client_name' => $validity[0]['username'],
                        'otp' => $reset_code,
                    ]);
                    $message = $OpsService->send_email_reg_otp($last_id, $validity[0]['username'], $workemail, $html_msg);
                    $obsfcate_email = preg_replace("/(?!^).(?=[^@]+@)/", "*", $workemail);
                    $respondWith['status'] = 'ok';
                    $respondWith['messages'] = "Hello " . $validity[0]['username'] . ". Please enter the OTP we sent to your email " . $obsfcate_email . ".";
                    return $this->json($respondWith);
                    exit;
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "We already have your account. Kindly login.";
                    return $this->json($respondWith);
                    exit;
                }
            }
        }
    }

    /**
     * @Route("/activate-otp-tr", name="Shared activate-otp-tr")
     */
    public function activate_otp()
    {
        $OpsService = new OpsService;
        $ver_otp = trim($_POST['ver_otp']);
        $assocemail = trim($_POST['assocemail']);
        if ($assocemail == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Error occured. Please contact support.";
            return $this->json($respondWith);
            exit;
        } else if ($ver_otp == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter the OTP we sent to your email.";
            return $this->json($respondWith);
            exit;
        } else {
            $repository = $this->getDoctrine()
                ->getRepository(Logins::class);
            $set_otp = $repository->get_acc_otp($assocemail);
            if (empty($set_otp)) {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "OTP purged. Please contact support.";
                return $this->json($respondWith);
            }
            if ($set_otp[0]['temp_otp'] == '') {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "OTP purged. Please contact support.";
                return $this->json($respondWith);
            }
            if ($ver_otp == $set_otp[0]['temp_otp']) {
                $repository = $this->getDoctrine()
                    ->getRepository(Logins::class);
                $repository->activat_acc_otp($assocemail);
                $respondWith['status'] = 'ok';
                $respondWith['messages'] = "You have activated your account! Now log in.";
                return $this->json($respondWith);
            } else {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "You have entred a wrong OTP.";
                return $this->json($respondWith);
            }
        }
    }

    /**
     * @Route("/logout-tr", name="log user out-tr")
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
     * @Route("/register-trainer", name="register-trainer")
     */
    public function register_trainer()
    {
        $sescontrol = new SessionController;
        $ops_service = new OpsService;
        $em = $this->getDoctrine()->getManager();
        $storeFolder = '/var/uploads/trainerdocuments/';
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
        if (isset($_FILES['trainerfiles'])) {
            $file_data = $_FILES['trainerfiles'];
        }
        $InputName = $_POST['InputName'];
        $InputEmail1 = $_POST['InputEmail1'];
        $mobile_number = $_POST['mobile-number'];
        $InputAddress = $_POST['InputAddress'];
        $login_password = $_POST['loginpassword'];
        $education_qualification = $_POST['educationqualification'];
        $competencies = $_POST['competencies'];
        $client_bio = $_POST['client-bio-field'];
        $InputEmail1 = $_POST['InputEmail1'];
        if ($InputName == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your name.";
            return $this->json($respondWith);
        } else if ($InputEmail1 == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your email address.";
            return $this->json($respondWith);
        } else if ($mobile_number == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your mobile number.";
            return $this->json($respondWith);
        } else if ($InputAddress == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your location address.";
            return $this->json($respondWith);
        } else if ($login_password == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your password.";
            return $this->json($respondWith);
        } else if ($education_qualification == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please select your education qualification.";
            return $this->json($respondWith);
        } else if ($competencies == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please enter your competencies.";
            return $this->json($respondWith);
        } else if ($client_bio == '') {
            $respondWith['status'] = 'fail';
            $respondWith['messages'] = "Please describe yourself briefly.";
            return $this->json($respondWith);
        } else {

            //$competencies = json_decode($competencies);

            //$selected_trainers = explode(',', $selected_trainers);

            $password = hash('ripemd160', $login_password);
            $repository = $this->getDoctrine()
                ->getRepository(Clients::class);
            $pre_saved = $repository->confirm_not_already_registered($InputEmail1);
            if (empty($pre_saved)) {
                $last_id = 0;
                $reset_code = $ops_service->generate_random_string($length = 6);
                $competencies = json_decode($competencies);

                $user_info = array(
                    'InputName' => $InputName,
                    'InputEmail1' => $InputEmail1,
                    'mobile_number' => $mobile_number,
                    'InputAddress' => $InputAddress,
                    'login_password' => $password,
                    'education_qualification' => $education_qualification,
                    'client_bio' => $client_bio,
                    'OTP' => $reset_code,
                    'is_trainer' => 1,
                );
                $user_info_with_id = $user_info;
                $user_info = new Clients;
                $user_info->setUserName($InputName);
                $user_info->setEmailAddress($InputEmail1);
                $user_info->setPhone($mobile_number);
                $user_info->setPassword($password);
                $user_info->setTempOtp($reset_code);
                $user_info->setIsTrainer(1);
                $user_info->setRole(2);
                $user_info->setDateOfJoining(new \Datetime());
                $user_info->setModifiedDate(new \Datetime());
                $em->persist($user_info);
                $em->flush();
                $last_id = $user_info->getRecordId();
                // var_dump($last_id);
                // exit;

                $user_info_with_id['client_id'] = $last_id;
                if ($last_id > 0) {
                    $repository = $this->getDoctrine()
                        ->getRepository(TrainerProfiles::class);
                    $trainer_id_frdb=$repository->save_trainer_profile($user_info_with_id);
                    // var_dump($trainer_id);
                    // exit;
                    $repository = $this->getDoctrine()
                        ->getRepository(TrainerProfiles::class);
                    $repository->save_trainer_profile_tr($user_info_with_id);

                    if (!empty($_FILES)) {
                        $file_datas = $this->save_trainer_files($last_id, $storeFolder, $upl_path);
                        foreach ($file_datas as $file_data):
                            $repository = $this->getDoctrine()
                                ->getRepository(TrainerProfiles::class);
                            $repository->set_upload_location($file_data);
                        endforeach;
                    } else {
                    }

                    foreach ($competencies as $competency):
                        $repository = $this->getDoctrine()
                            ->getRepository(TrainerProfiles::class);
                        $repository->save_trainer_profile_competencies($trainer_id_frdb, $competency->value);
                    endforeach;

                    $respondWith['status'] = 'ok';
                    $respondWith['messages'] = "Welcome " . $InputName . ". Your profile is under review, we will alert you via email upon completion.";
                    return $this->json($respondWith);
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "Something went wrong. Please try again.";
                    return $this->json($respondWith);
                }
            } elseif (!empty($pre_saved)) {
                if ($pre_saved[0]['is_trainer'] == 1) {
                    $traiver_verification_status = $pre_saved[0]['traiver_verification_status'];
                    if ($traiver_verification_status == 0) {
                        $respondWith['status'] = 'fail';
                        $respondWith['messages'] = "Your account is pending verification, we will contact you when verified.";
                        return $this->json($respondWith);
                    }
                } else {
                    $respondWith['status'] = 'fail';
                    $respondWith['messages'] = "Registration failed. Kindly contact support.";
                    return $this->json($respondWith);
                }
            } else {
                $respondWith['status'] = 'fail';
                $respondWith['messages'] = "We already have your account. Kindly login.";
                return $this->json($respondWith);
            }
        }
        $respondWith['status'] = 'fail';
        $respondWith['messages'] = "Registration failed. Kindly contact support.";
        return $this->json($respondWith);
    }

    public function save_trainer_files($record_id, $storeFolder, $upl_path)
    {
        $file_data = array();
        $countfiles = count($_FILES);
        $transaction_type = null;
        try {
            foreach ($_FILES as $key => $value) {
                $countfiles = count($value['name']);
                for ($i = 0; $i < $countfiles; $i++) {
                    $theid = time() . rand();
                    $targetFile = $storeFolder . $value['name'][$i];
                    $tempFile = $value['tmp_name'][$i];
                    $file_ext = substr($targetFile, strripos($targetFile, '.'));
                    move_uploaded_file($tempFile, $upl_path . $theid . $file_ext);
                    $file_data[] = array(
                        'record_id' => $record_id,
                        'upload_type' => $transaction_type,
                        'file_path' => $storeFolder . $theid . $file_ext,
                    );
                }
            }
            return ($file_data);
        } catch (Exception $error) {
            return $file_data;
        }
    }

    /**
     * @Route("/trainer-vw-my-sessions", name="trainer-vw-my-sessions")
     */
    public function trainer_vw_my_sessions()
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
        $trainer_profile = $repository->get_trainer_profile($userid);

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
}
