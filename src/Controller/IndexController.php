<?php
namespace App\Controller;

use App\Controller\SessionController;
use App\Service\OpsService;
use App\Entity\CmsAboutUs;
use App\Entity\Blog;
use App\Entity\Clients;
use App\Entity\Services;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $session;
    protected $projectDir;
    public function __construct(SessionInterface $session, KernelInterface $kernel)
    {
        $this->session = $session;
        $this->projectDir = $kernel->getProjectDir();
    }
    /**
     *
     * SKIN
     *
     */

    /**
     * @Route("/", name="index slash")
     * @Route("/home", name="index home")
     * @Route("/index", name="index index")
     */
    public function index(): Response
    {
        
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $gallery = $repository->get_gallery();

        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $cows = $repository->get_active_all_cow_data();
        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $why_us = $repository->get_whyus_active_only();
        
        $repository = $this->getDoctrine()
            ->getRepository(Services::class);
        $list_services = $repository->get_all_service_list();


        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_about_us();

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo_vals = $repository->get_core_values_active_only();

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo_mission = $repository->get_mission();


        // return $this->render('index/about.html.twig', [
        //     'controller_name' => 'IndexController',
        //     'page_info' =>  $pageinfo[0],
        //     'page_values' =>  $pageinfo_vals,
        //     'page_mission' =>  $pageinfo_mission[0],
        // ]);

        ///about

        // $repository = $this->getDoctrine()
        //     ->getRepository(CmsAboutUs::class);
        // $why_taalam = $repository->get_about_us_why_taalam();

        // var_dump($why_taalam);
        // exit;
        
        //abt end

        foreach ($cows as $result):
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

            $user_id=$coach_ids[0]['client_look_up_id'];
            
            $repository = $this->getDoctrine()
                ->getRepository(Blog::class);
            $trainer_ratings = $repository->get_trainer_rating($coach_id);

            $trainer_rating = $trainer_ratings[0]['rating'];
            if($trainer_rating == null){
                $trainer_rating =0;
            }else{
                $trainer_rating = ceil($trainer_rating);
            }
            
            $on_date = date('dS-M Y', strtotime($on_date));

            $ops_service = new OpsService;
            $dropdown = '';
            $ops_service_response = $ops_service->get_cow_params($record_id, $status,$img_url,$trainer_rating);
            $dropdown = $ops_service_response['dropdown'];
            $unit_ui_display = $ops_service_response['unit_ui_display'];
            $rating_text = $ops_service_response['rating_text'];

            //$img_url;

            if($img_url=='#'){
                $img_url='resources/skin/images/testimonial/1.jpg';
            }
            $returnarray[] = array(
                'user_name'=>$user_name,
                'award_text'=>$award_text,
                'trainer_rating'=>$trainer_rating,
                //$display_date,
                'rating_text'=>$rating_text,
                'unit_ui_display'=>$unit_ui_display,
                'dropdown'=>$dropdown,
            );
        endforeach;

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'gallery'=>$gallery,
            'cows'=>$returnarray,
            'why_uses'=>$why_us,
            'list_services'=>$list_services,            
            'page_values' =>  $pageinfo_vals,
            //'why_taalam'=>$why_taalam[0],
        ]);
    }
    /**
     * @Route("/client-auth", name="client_side auth")
     */
    public function view_client_auth(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $guide = $repository->get_guide_active_only();

        $pageinfo = array(
            'page_name' => 'Client Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                $pageinfo['guide']= $guide;
                return $this->render('client_side/dashboard.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/fe-auth-forgot-password", name="fe-auth-forgot-password auth")
     */
    public function view_client_forgot_password(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/forgot-password.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/forgot-password.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/forgot-password.html.twig');
        } else {
            return $this->render('client_side/forgot-password.html.twig');
        }
    }
    /**
     * @Route("/client-registration", name="client_side registration")
     */
    public function view_client_registration(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Registration',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/register.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/register.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/register.html.twig');
        } else {
            return $this->render('client_side/register.html.twig');
        }
    }
    /**
     * @Route("/client-dash", name="client_side dashboard")
     */
    public function view_client_dash(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $guide = $repository->get_guide_active_only();

        $pageinfo = array(
            'page_name' => 'Client Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                $pageinfo['guide']= $guide;
                return $this->render('client_side/dashboard.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-sessions", name="client_side client-vw-items")
     */
    public function view_client_items(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Sessions',
            'page_description' => 'Manage your sessions',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-own-profile", name="client-vw-own-profile client-vw-items")
     */
    public function view_client_own_profile(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Profile',
            'page_description' => 'Manage my profile',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_profile.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-services-offered", name="client-vw-services-offered services-offered")
     */
    public function view_services(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Services',
            'page_description' => 'Our Services',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_services.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-book-event", name="client-vw-book-event book-event")
     */
    public function view_client_book_event(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Events',
            'page_description' => 'Event upcoming listing',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_book_event.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-book-ongoing-event", name="client-vw-book-ongoing-event book-event")
     */
    public function view_client_book_ongoing_event(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Events',
            'page_description' => 'Event ongoing listing',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_book_ongoing_event.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-book-trainer-session", name="client-vw-book-trainer-session book-trainerd")
     */
    public function view_client_book_trainer_session(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Book session',
            'page_description' => 'Book a trainer',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_book_trainers.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-book-event-more-details", name="client-vw-book-event-more-details book-trainerd")
     */
    public function view_client_book_event_more_details(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Book session',
            'page_description' => 'Book an event',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_book_event_view.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-booked-event-more-details", name="client-vw-booked-event-more-details book-trainerd")
     */
    public function view_client_booked_event_more_details(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Booked session',
            'page_description' => 'Booked event',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_booked_event_view.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    
    /**
     * @Route("/client-vw-chat-room", name="client-vw-chat-room")
     */
    public function view_client_chat_room(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client chatroom',
            'page_description' => 'Client chat room',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_chat_room.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }


    public function verifySession()
    {
        $sescontrol = new SessionController;
        $session = $this->session;
        $sesStatus = 0;
        $userid = 0;
        $token = '';
        if (!$this->session) {
            $sesStatus = 0;
        } else {
            $userid = $this->session->get('dropshopuid');
            $token = $this->session->get('token');
            if ($token == '') {
                $sesStatus = 0;
            } else {
                $sesStatus = 1;
            }
            if ($userid == '' || $userid == null) {
                $respondWith['status'] = 'killsess';
                $respondWith['messages'] = "bad session.";
                $retval = array(
                    'in pos' => 'failed in 2 1<br>',
                    'status' => 'badsession',
                    'minutes' => 0,
                );
                return $retval;
                exit;
            } else {
                $sesStatus = 1;
            }
        }
        if ($token != '') {
            $sesstatus = $sescontrol->verifyJwt($token);
            if (isset($sesstatus['exp'])) {
                $sestill = $sesstatus['exp'];
                $sestill = date('Y-m-d H:i:s', strtotime($sestill));
                $timme = date('Y-m-d H:i:s');
                $diff = (new \DateTime($timme))->diff(new \DateTime($sestill));
                $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                $retval = array(
                    'in pos' => 'failed in 1 1<br>',
                    'status' => 'expired',
                    'minutes' => 0,
                );
            }
            if ($sesstatus['status'] == 'ok') {
                $respondWith['status'] = 'ok';
                $respondWith['messages'] = "active session. Till:" . $sesstatus['exp'];
                $respondWith['token'] = $sesstatus['token'];
                $respondWith['userRole'] = $sesstatus['userRole'];
                $retval = array(
                    'in pos' => 'passed in 3 1<br>',
                    'status' => 'ok',
                    'minutes' => $minutes,
                    'userRole' => $sesstatus['userRole'],
                );
            } else {
                $respondWith['status'] = 'killsess';
                $respondWith['messages'] = "bad session.";
                $retval = array(
                    'in pos' => 'failed in 2 1<br>',
                    'status' => 'badsession',
                    'minutes' => 0,
                );
            }
        } else {
            $respondWith['status'] = 'false';
            $respondWith['messages'] = "Not logged in.";
            $retval = array(
                'in pos' => 'failed in 3 1<br>',
                'status' => 'nosession',
                'minutes' => 0,
            );
        }
        return $retval;
    }
    public function verify_admin_session()
    {
        $sescontrol = new SessionController;
        $session = $this->session;
        $sesStatus = 0;
        $userid = 0;
        $token = '';
        if (!$this->session) {
            $sesStatus = 0;
        } else {
            $userid = $this->session->get('dsladminuid');
            $token = $this->session->get('token');
            if ($token == '') {
                $sesStatus = 0;
            } else {
                $sesStatus = 1;
            }
            if ($userid == '' || $userid == null) {
                $respondWith['status'] = 'killsess';
                $respondWith['messages'] = "bad session.";
                $retval = array(
                    'in pos' => 'failed in 2 1<br>',
                    'status' => 'badsession',
                    'minutes' => 0,
                );
                return $retval;
                exit;
            } else {
                $sesStatus = 1;
            }
        }
        if ($token != '') {
            $sesstatus = $sescontrol->verifyJwt($token);
            if (isset($sesstatus['exp'])) {
                $sestill = $sesstatus['exp'];
                $sestill = date('Y-m-d H:i:s', strtotime($sestill));
                $timme = date('Y-m-d H:i:s');
                $diff = (new \DateTime($timme))->diff(new \DateTime($sestill));
                $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                $retval = array(
                    'in pos' => 'failed in 1 1<br>',
                    'status' => 'expired',
                    'minutes' => 0,
                );
            }
            if ($sesstatus['status'] == 'ok') {
                $respondWith['status'] = 'ok';
                $respondWith['messages'] = "active session. Till:" . $sesstatus['exp'];
                $respondWith['token'] = $sesstatus['token'];
                $respondWith['userRole'] = $sesstatus['userRole'];
                $retval = array(
                    'in pos' => 'passed in 3 1<br>',
                    'status' => 'ok',
                    'minutes' => $minutes,
                    'userRole' => $sesstatus['userRole'],
                );
            } else {
                $respondWith['status'] = 'killsess';
                $respondWith['messages'] = "bad session.";
                $retval = array(
                    'in pos' => 'failed in 2 1<br>',
                    'status' => 'badsession',
                    'minutes' => 0,
                );
            }
        } else {
            $respondWith['status'] = 'false';
            $respondWith['messages'] = "Not logged in.";
            $retval = array(
                'in pos' => 'failed in 3 1<br>',
                'status' => 'nosession',
                'minutes' => 0,
            );
        }
        return $retval;
    }
    /**
     * @Route("/current-activities", name="current-activities")
     */
    public function current_activities(): Response
    {        
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_about_us_event_cms();

        return $this->render('index/current-activities.html.twig', [
            'controller_name' => 'IndexController',
            'page_info' => $pageinfo[0]
        ]);
    }

    /**
     * @Route("/current-activities-view", name="current-activities-view")
     */
    public function current_activities_view(): Response
    {
        return $this->render('index/current-activities-view.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/about", name="about-view")
     */
    public function about_view(): Response
    {
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_about_us();

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo_vals = $repository->get_core_values_active_only();

        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo_mission = $repository->get_mission();


        return $this->render('index/about.html.twig', [
            'controller_name' => 'IndexController',
            'page_info' =>  $pageinfo[0],
            'page_values' =>  $pageinfo_vals,
            'page_mission' =>  $pageinfo_mission[0],
        ]);
    }

    /**
     * @Route("/sema-nasi", name="sema-nasi")
     */
    public function sema_nasi(): Response
    {
        return $this->render('index/sema-nasi.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/mission", name="mission")
     */
    public function mission(): Response
    {
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_mission();

        return $this->render('index/mission.html.twig', [
            'controller_name' => 'IndexController',
            'page_info' =>  $pageinfo[0]
        ]);
    }
    /**
     * @Route("/core-values", name="core-values")
     */
    public function core_values(): Response
    {
        
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_values();

        return $this->render('index/core-values.html.twig', [
            'controller_name' => 'IndexController',
            'page_info' =>  $pageinfo[0]
        ]);
    }
    /**
     * @Route("/faqs", name="faqs")
     */
    public function faqs(): Response
    {
        
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_faqs_active_only();

        return $this->render('index/faqs.html.twig', [
            'controller_name' => 'IndexController',
            'faqs'=>$results
        ]);
    }
    /**
     * @Route("/health-and-safety", name="health-and-safety")
     */
    public function health_and_safety(): Response
    {
        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $results = $repository->get_hes_active_only();

        return $this->render('index/health-and-safety.html.twig', [
            'controller_name' => 'IndexController',
            'faqs'=>$results
        ]);
    }
    /**
     * @Route("/personal-coaching", name="personal-coaching")
     */
    public function personal_coaching(): Response
    {
        $pageinfo = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $pageinfo = $repository->get_personal_coaching();
       
        return $this->render('index/personal-coaching.html.twig', [
            'controller_name' => 'IndexController',
            'page_info'=>$pageinfo[0]
        ]);
    }
    /**
     * @Route("/client-experiences", name="client-experiences")
     */
    public function client_experiences(): Response
    {
        $page_info = array();

        $repository = $this->getDoctrine()
            ->getRepository(CmsAboutUs::class);
        $page_info = $repository->get_testimonials();

        return $this->render('index/client-experiences.html.twig', [
            'controller_name' => 'IndexController',
            'page_info'=>$page_info
        ]);
    }
    /**
     * @Route("/blog", name="blog")
     */
    public function blog(): Response
    {
        return $this->render('index/blog.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/blog-view", name="blog-view")
     */
    public function blog_view(): Response
    {
        return $this->render('index/blog-details.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/opportunities", name="opportunities")
     */
    public function opportunities(): Response
    {
        return $this->render('index/opportunities.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/opportunity-view", name="opportunity-view")
     */
    public function opportunity_view(): Response
    {
        return $this->render('index/opportunities-view.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/apply-opp", name="apply-opp")
     */
    public function opportunity_apply_view(): Response
    {
        return $this->render('index/opportunities-apply-view.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    /**
     * @Route("/client-vw-registered-kids", name="client-vw-registered-kids kids")
     */
    public function view_client_registered_kids(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Kids',
            'page_description' => 'Kids View',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_registered_kids.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-registered-chaperone", name="client-vw-registered-chaperone chaperone")
     */
    public function view_client_registered_chaperone(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Kids',
            'page_description' => 'Kid chaperone View',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_registered_chaperone.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-edit-registered-kids", name="client-vw-edit-registered-kids kids")
     */
    public function view_client_edit_registered_kids(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Kids',
            'page_description' => 'Kids Edit',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_edit_registered_kids.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-edit-registered-tiles/{record_id}/", name="client-vw-edit-registered-tiles tiles")
     */
    public function view_admin_edit_registered_tiles(int $record_id): Response
    {
        // var_dump($record_id);
        // exit;
        if(!is_numeric($record_id)){
            $pageinfo = array(
                'page_name' => 'Admin gallery',
                'page_description' => 'Manage gallery!',
            );
            $nav = $navGen->defaultUserNavigen($role = null);
            $pageinfo['nav'] = $nav;
            return $this->render('admin_side/01admin_manage_tiles.html.twig', $pageinfo);
        }
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        
        $sesok = $this->verify_admin_session();
        
        $repository = $this->getDoctrine()
            ->getRepository(Blog::class);
        $event_tile_details = $repository->get_eventtitle_details($record_id);

        $pageinfo = array(
            'page_name' => 'Tiles',
            'page_description' => 'Tiles Edit',
            'event_tile_details' => $event_tile_details[0]
        );

        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/view_edit_registered_tiles.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-edit-registered-chaperone", name="client-vw-edit-registered-chaperone kids")
     */
    public function view_client_edit_registered_chaperone(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Chaperone',
            'page_description' => 'Chaperone Edit',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_edit_registered_chaperone.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }
    /**
     * @Route("/client-vw-client-experience", name="client-vw-client-experience")
     */
    public function view_client_client_experience(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Client Experience',
            'page_description' => 'Client Experience',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('client_side/view_client_experience.html.twig', $pageinfo);
            } else {
                return $this->render('client_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('client_side/index.html.twig');
        } else {
            return $this->render('client_side/index.html.twig');
        }
    }






    /**
     *
     * ADMIN
     *
     */





    /**
     * @Route("/admin-auth", name="admin_side auth")
     */
    public function view_admin_auth(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/dashboard.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/resetpsaction-adm", name="Shared resetpsaction")
     */
    public function reset_ps_action_adm()
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
     * @Route("/activate-otp-adm", name="Shared activate-otp")
     */
    public function activate_otp_adm()
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
     * @Route("/admin-dash", name="admin_side dashboard")
     */
    public function view_admin_dash(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/dashboard.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    
    /**
     * @Route("/admin-vw-items", name="admin_side admin-vw-items")
     */
    public function view_admin_items(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin items',
            'page_description' => 'Manage storage units',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/view_shelves.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-new-trainers", name="admin_side admin-vw-new-trainers")
     */
    public function view_admin_new_trainers(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin items',
            'page_description' => 'Manage trainers',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/new_trainers_vw.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-clients", name="admin-vw-clients")
     */
    public function view_admin_clients(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin Clients',
            'page_description' => 'View Clients!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/client_view.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-trainers", name="admin-vw-trainers")
     */
    public function view_admin_trainerss(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin trainers',
            'page_description' => 'View trainers!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/trainers_view.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-all-sessions", name="admin-vw-all-sessions")
     */
    public function view_admin_all_sessions(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin sessions',
            'page_description' => 'View new unconfirmed sessions!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all_booked_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-all-event-trainers", name="admin-vw-all-event-trainers")
     */
    public function view_admin_all_event_trainers(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin sessions',
            'page_description' => 'View new unconfirmed sessions!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all_event_trainers.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-ongoing-sessions", name="admin-vw-ongoing-sessions")
     */
    public function view_admin_ongoing_sessions(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin sessions',
            'page_description' => 'View ongoing sessions!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all_ongoing_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-canceled-sessions", name="admin-vw-canceled-sessions")
     */
    public function view_admin_canceled_sessions(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin sessions',
            'page_description' => 'View canceled sessions!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all_canceled_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-complete-sessions", name="admin-vw-complete-sessions")
     */
    public function view_admin_complete_sessions(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin sessions',
            'page_description' => 'View completed sessions!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all_complete_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-create-event", name="admin-vw-vw-create-event")
     */
    public function view_admin_vw_create_event(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin events',
            'page_description' => 'Create new event!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_event.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-edit-event", name="admin-vw-edit-event")
     */
    public function view_admin_vw_admin_edit_event(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin events',
            'page_description' => 'Edit an event!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_edit_event.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-create-blog", name="admin-vw-admin-vw-create-blog")
     */
    public function view_admin_vw_admin_vw_create_blog(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin create blog',
            'page_description' => 'Edit a blog!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_blog.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-create-pdf-blog", name="admin-vw-admin-vw-create-pdf-blog")
     */
    public function view_admin_vw_admin_vw_create_pdf_blog(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin create blog',
            'page_description' => 'Edit a pdf blog!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_pdf_blog.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-upload-gallery", name="admin-vw-admin-vw-upload-gallery")
     */
    public function view_admin_vw_upload_gallery(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin gallery',
            'page_description' => 'Upload Images!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_gallery.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    

    /**
     * @Route("/admin-vw-upload-tiles", name="admin-vw-admin-vw-upload-tiles")
     */
    public function view_admin_vw_upload_tiles(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin tiles',
            'page_description' => 'Manage tiles!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/01create_tiles.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    
    /**
     * @Route("/admin-vw-manage-gallery", name="admin-vw-manage-gallery")
     */
    public function admin_vw_manage_gallery(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin gallery',
            'page_description' => 'Manage gallery!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/01admin_manage_tiles.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-manage-tiles", name="admin-vw-manage-tiles")
     */
    public function admin_vw_manage_tiles(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin gallery',
            'page_description' => 'Manage gallery!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/01admin_manage_tiles.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-manage-cow", name="admin-vw-cow")
     */
    public function admin_vw_manage_cow(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin coaches',
            'page_description' => 'Coaches of the week!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/admin_manage_cow.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-manage-why-choose", name="admin-vw-why-choose")
     */
    public function admin_vw_manage_why_choose(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin Manage',
            'page_description' => 'Why choose us?',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/admin_manage_why_choose.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-manage-why-taalam", name="admin-vw-why-taalam")
     */
    public function admin_vw_manage_why_taalam(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin Manage',
            'page_description' => 'Why taalam?',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/admin_manage_why_taalam.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    
    /**
     * @Route("/admin-vw-nominate-cow", name="admin-nominate-cow")
     */
    public function admin_vw_nominate_cow(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin coaches',
            'page_description' => 'Nominate coaches of the week!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/admin_manage_cow.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }

    /**
     * @Route("/admin-vw-new-trainers-details", name="admin_side admin-vw-new-trainers-details")
     */
    public function view_admin_new_trainers_details(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin items',
            'page_description' => 'Manage trainers',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/new-trainer-details.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-new-opportunity", name="admin-vw-new-opportunity")
     */
    public function view_admin_new_opportunity(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin opportunities',
            'page_description' => 'Manage opportunities',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/new-opportunities.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-opportunity-list", name="admin-vw-opportunity-list")
     */
    public function view_admin_new_opportunity_list(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin opportunities',
            'page_description' => 'Manage opportunities',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/all-opportunities.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-edit-opportunity", name="admin-vw-edit-opportunity")
     */
    public function view_admin_edit_opportunity(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Admin opportunity',
            'page_description' => 'Edit an opportunity!',
        );
        $sesok = $this->verify_admin_session();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/create_edit_opportunity.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('admin_side/index.html.twig');
        } else {
            return $this->render('admin_side/index.html.twig');
        }
    }
    /**
     * @Route("/admin-vw-blog-list", name="admin-vw-blog-list")
     */
     public function view_admin_new_blog_list(): Response
     {
         $navGen = new NavGeneratorController;
         $ops_service = new OpsService;
         $pageinfo = array(
             'page_name' => 'Admin blogs',
             'page_description' => 'Manage blogs',
         );
         $sesok = $this->verify_admin_session();
         if ($sesok['status'] == 'ok') {
             if ($sesok['userRole'] == 2) {
                 $nav = $navGen->defaultUserNavigen($role = null);
                 $pageinfo['nav'] = $nav;
                 return $this->render('admin_side/all-blogs.html.twig', $pageinfo);
             } else {
                 return $this->render('admin_side/index.html.twig');
             }
         } else if ($sesok['status'] == 'expired') {
             return $this->render('admin_side/index.html.twig');
         } else {
             return $this->render('admin_side/index.html.twig');
         }
     }
     /**
      * @Route("/admin-vw-edit-blog", name="admin-vw-edit-blog")
      */
     public function view_admin_edit_blog(): Response
     {
         $navGen = new NavGeneratorController;
         $ops_service = new OpsService;
         $pageinfo = array(
             'page_name' => 'Admin blog',
             'page_description' => 'Edit an blog!',
         );
         $sesok = $this->verify_admin_session();
         if ($sesok['status'] == 'ok') {
             if ($sesok['userRole'] == 2) {
                 $nav = $navGen->defaultUserNavigen($role = null);
                 $pageinfo['nav'] = $nav;
                 return $this->render('admin_side/create_edit_blog.html.twig', $pageinfo);
             } else {
                 return $this->render('admin_side/index.html.twig');
             }
         } else if ($sesok['status'] == 'expired') {
             return $this->render('admin_side/index.html.twig');
         } else {
             return $this->render('admin_side/index.html.twig');
         }
     }
     /**
      * @Route("/admin-vw-opps-app-list", name="admin-vw-opps-app-list")
      */
      public function view_admin_opps_app_list(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin opportunities',
              'page_description' => 'Manage opportunities',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/all-opportunities-view.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-opp-app", name="admin-vw-edit-opp-app")
       */
      public function view_admin_edit_opp_app(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin opportunities',
              'page_description' => 'Edit an opportunities!',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/view_opp_app.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-event-customers", name="admin-vw-event-customers")
       */
      public function view_admin_vw_event_customers(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin events',
              'page_description' => 'Member paid events!',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/view_event_members.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-mark-attendance", name="admin-vw-mark-attendance")
       */
      public function view_admin_vw_mark_attendance(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin events',
              'page_description' => 'Member attendance!',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/view_event_member_attendance.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-cms-about-us", name="admin-vw-cms-about-us")
       */
      public function view_admin_vw_cms_about_us(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'About Us',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_about_us.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-cms-mission", name="admin-vw-cms-mission")
       */
      public function view_admin_vw_cms_mission(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'About Us-mission',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_about_us_mission.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-cms-content", name="admin-vw-cms-content")
       */
      public function view_admin_vw_cms_content(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'About Upcoming Events',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_upcoming_events.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
    //   /**
    //    * @Route("/admin-vw-cms-values", name="admin-vw-cms-values")
    //    */
    //   public function view_admin_vw_cms_values(): Response
    //   {
    //       $navGen = new NavGeneratorController;
    //       $ops_service = new OpsService;
    //       $pageinfo = array(
    //           'page_name' => 'Admin CMS',
    //           'page_description' => 'About Core-values',
    //       );
    //       $sesok = $this->verify_admin_session();
    //       if ($sesok['status'] == 'ok') {
    //           if ($sesok['userRole'] == 2) {
    //               $nav = $navGen->defaultUserNavigen($role = null);
    //               $pageinfo['nav'] = $nav;
    //               return $this->render('admin_side/cms_about_us_values.html.twig', $pageinfo);
    //           } else {
    //               return $this->render('admin_side/index.html.twig');
    //           }
    //       } else if ($sesok['status'] == 'expired') {
    //           return $this->render('admin_side/index.html.twig');
    //       } else {
    //           return $this->render('admin_side/index.html.twig');
    //       }
    //   }
      
      /**
       * @Route("/admin-vw-cms-values", name="admin-vw-cms-values")
       */
      public function view_admin_vw_cms_values(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'About Core-values',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  //return $this->render('admin_side/cms_about_us_values.html.twig', $pageinfo);
                  return $this->render('admin_side/cms_support_core-val-revamp.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
              if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('admin_side/cms_about_us_values.html.twig', $pageinfo);
            } else {
                return $this->render('admin_side/index.html.twig');
            }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      /**
       * @Route("/admin-vw-cms-faq", name="admin-vw-cms-faq")
       */
      public function view_admin_vw_cms_faq(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'About FAQs',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_support_faqs.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }
      
      /**
       * @Route("/admin-vw-client-guide", name="admin-vw-client-guide")
       */
      public function view_admin_vw_client_guide(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Parent Guide',
              'page_description' => 'Parent Guide',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_parent_guide.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }

      /**
       * @Route("/admin-vw-cms-hes", name="admin-vw-cms-hes")
       */
      public function view_admin_vw_cms_hes(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'Health and services',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_support_hes.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }

      /**
       * @Route("/admin-vw-cms-personal-coaching", name="admin-vw-cms-personal-coaching")
       */
      public function view_admin_vw_cms_personal_coaching(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'Personal Coaching',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_support_personal_coaching.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }

      /**
       * @Route("/admin-vw-cms-manage-testimonial", name="admin-vw-cms-manage-testimonial")
       */
      public function view_admin_vw_cms_manage_testimonial(): Response
      {
          $navGen = new NavGeneratorController;
          $ops_service = new OpsService;
          $pageinfo = array(
              'page_name' => 'Admin CMS',
              'page_description' => 'Testimonials',
          );
          $sesok = $this->verify_admin_session();
          if ($sesok['status'] == 'ok') {
              if ($sesok['userRole'] == 2) {
                  $nav = $navGen->defaultUserNavigen($role = null);
                  $pageinfo['nav'] = $nav;
                  return $this->render('admin_side/cms_manage_testimonials.html.twig', $pageinfo);
              } else {
                  return $this->render('admin_side/index.html.twig');
              }
          } else if ($sesok['status'] == 'expired') {
              return $this->render('admin_side/index.html.twig');
          } else {
              return $this->render('admin_side/index.html.twig');
          }
      }


    /**
     *
     * TRAINERS
     *
     */

    /**
     * @Route("/trainer-auth", name="trainer_side auth")
     */
    public function trainer_auth(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Trainer Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('trainer_side/dashboard.html.twig', $pageinfo);
            } else {
                return $this->render('trainer_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('trainer_side/index.html.twig');
        } else {
            return $this->render('trainer_side/index.html.twig');
        }
    }
    /**
     * @Route("/trainer-registration", name="trainer_side registration")
     */
    public function trainer_registration(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Trainer Registration',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 1) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('trainer_side/register.html.twig', $pageinfo);
            } else {
                return $this->render('trainer_side/register.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('trainer_side/register.html.twig');
        } else {
            return $this->render('trainer_side/register.html.twig');
        }
    }

    /**
     * @Route("/trainer-dash", name="trainer_side dashboard")
     */
    public function view_trainer_dash(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'Trainer Home',
            'page_description' => 'Welcome to Taalam Care!',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('trainer_side/all_complete_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('trainer_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('trainer_side/index.html.twig');
        } else {
            return $this->render('trainer_side/index.html.twig');
        }
    }

    /**
     * @Route("/trainer-my-sessions", name="trainer-my-sessions client-vw-items")
     */
    public function view_trainer_my_sessions(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'My Sessions',
            'page_description' => 'Manage your sessions',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('trainer_side/all_complete_sessions.html.twig', $pageinfo);
            } else {
                return $this->render('trainer_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('trainer_side/index.html.twig');
        } else {
            return $this->render('trainer_side/index.html.twig');
        }
    }

    /**
     * @Route("/trainer-view-event-details-v1", name="trainer-view-event-details-v1 client-vw-items")
     */
    public function view_trainer_event_details(): Response
    {
        $navGen = new NavGeneratorController;
        $ops_service = new OpsService;
        $pageinfo = array(
            'page_name' => 'My Sessions',
            'page_description' => 'Manage your sessions',
        );
        $sesok = $this->verifySession();
        if ($sesok['status'] == 'ok') {
            if ($sesok['userRole'] == 2) {
                $nav = $navGen->defaultUserNavigen($role = null);
                $pageinfo['nav'] = $nav;
                return $this->render('trainer_side/view_event_details.html.twig', $pageinfo);
            } else {
                return $this->render('trainer_side/index.html.twig');
            }
        } else if ($sesok['status'] == 'expired') {
            return $this->render('trainer_side/index.html.twig');
        } else {
            return $this->render('trainer_side/index.html.twig');
        }
    }
}
