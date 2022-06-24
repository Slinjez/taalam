<?php
namespace App\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class OpsService extends AbstractController
{
    private $session;
    protected $projectDir;

    // public function __construct(SessionInterface $session, KernelInterface $kernel)
    // {
    //     $this->session = $session;
    //     $this->projectDir = $kernel->getProjectDir();
    // }

    //  /**
    //  * @required
    //  */
    // public function setMailer(SessionInterface $session, KernelInterface $kernel): void
    // {

    /**
     * @required
     * @return static
     */
    public function withSession(SessionInterface $session, KernelInterface $kernel): self
    {
        echo 'calling with session';
        $this->session = $session;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function getHappyMessage()
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function generate_random_string($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function send_email_reg_otp($last_id, $username, $user_mail, $html_msg)
    {
        $compute_array = array(
            'username' => '',
            'email' => $user_mail,
            'message-html' => $html_msg,
            'message-text' => $html_msg,
            'subject' => 'Registration OTP',
        );

        $mail_sender = $_ENV['email_sender'];
        $mail_sender_credentials = $_ENV['email_cred'];
        $ticket_email_sender = $_ENV['email_sender_name'];

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $mail_sender;
            $mail->Password = $mail_sender_credentials;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom($mail_sender, $ticket_email_sender);

            $mail->addAddress($user_mail, $username);

            //$mail->addBCC($ticket_email_receiver_email, $ticket_email_receiver_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $compute_array['subject'];
            $mail->Body = $compute_array['message-html'];
            $mail->AltBody = $compute_array['message-text'];

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function get_session_level_params($rating, $record_id, $status)
    {
        if ($rating == 0) {
            $rating_text = 'Not rated';
        } elseif ($rating == 1) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 2) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 3) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 4) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 5) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i>';
        } else {
            //pass
        }

        if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Make Payment</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Pending Payment</span>';
        } elseif ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-light btn-sm btn-block radius-30 centered-text">Pending trainer confirmation</span>';
        } elseif ($status == 3) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Paid and ongoing</span>';
        } elseif ($status == 4) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-primary btn-sm btn-block radius-30 centered-text">Complete - <small>' . $rating_text . '</small></span>';
        } elseif ($status == 5) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/#/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">canceled </span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    public function get_booked_session_level_params($rating, $record_id, $status)
    {
        if ($rating == 0) {
            $rating_text = 'Not rated';
        } elseif ($rating == 1) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 2) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 3) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 4) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 5) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i>';
        } else {
            //pass
        }

        
        $dropdown = '<div class="btn-group">'
        . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
        . '<div class="dropdown-menu">'
        . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
        . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >Make Payment</a><br>'
        . '</div>'
        . '</div>';
        $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Pending Payment</span>';

        if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '<a class="dropdown-item m actionbutton" attr-act="0" attr-id="' . $record_id . '" href="#" >Cancel</a><br>'
                . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >Make Payment</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Pending Payment</span>';
        } elseif ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="0" attr-id="' . $record_id . '"  href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >Cancel</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-light btn-sm btn-block radius-30 centered-text">Pending trainer confirmation</span>';
        } elseif ($status == 3) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="0" attr-id="' . $record_id . '"  href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Paid and ongoing</span>';
        } elseif ($status == 4) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-primary btn-sm btn-block radius-30 centered-text">Complete - <small>' . $rating_text . '</small></span>';
        } elseif ($status == 5) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" href="/client-vw-booked-event-more-details/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">canceled </span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_trainer_session_level_params($rating, $record_id, $status)
    {
        if ($rating == 0) {
            $rating_text = 'Not rated';
        } elseif ($rating == 1) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 2) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 3) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 4) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 5) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i>';
        } else {
            //pass
        }

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" href="/trainer-view-event-details-v1/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
            . '</div>'
            . '</div>';
        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Upcoming</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Ongoing</span>';
        } else if ($status == 3) {
            $unit_ui_display = '<span class=" badge-light btn-sm btn-block radius-30 centered-text">Complete</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_event_params($record_id, $status)
    {
        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton-main" href="/admin-vw-edit-event/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
            . '<a class="dropdown-item m actionbutton-main"  attr-act=0 attr-id=' . $record_id . '  href="/admin-vw-event-customers/?rec-id=' . $record_id . '" >View Members</a><br>'
            . '<a class="dropdown-item m actionbutton-main" href="/admin-vw-mark-attendance/?rec-id=' . $record_id . '&action=2" >Attendance Register</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set Icebox</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Upcoming</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=2 attr-id=' . $record_id . '  href="#" >Set Ongoing</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=3 attr-id=' . $record_id . '  href="#" >Set Complete</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=4 attr-id=' . $record_id . '  href="#" >Hide</a><br>'
            . '</div>'
            . '</div>';
        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm  radius-30 centered-text">Upcoming</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm  radius-30 centered-text">Ongoing</span>';
        } else if ($status == 3) {
            $unit_ui_display = '<span class=" badge-light btn-sm  radius-30 centered-text">Complete</span>';
        }else if ($status == 4) {
            $unit_ui_display = '<span class=" badge-light btn-sm  radius-30 centered-text">Hidden</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_attendance_params($record_id, $status)
    {
        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton-details"  attr-act=0 attr-id=' . $record_id . '  href="#" >View Medical Details</a><br>'
            . '</div>'
            . '</div>';
            $check_box='<input type="checkbox" class="attendance-actionbutton" child-id="'.$record_id.'"><span></span>';
            $unit_ui_display = '<span class=" badge-info btn-sm radius-30 centered-text">Absent</span>';
        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm radius-30 centered-text">Absent</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm  radius-30 centered-text">Present</span>';
        } 
        $response = array(
            'check_box'=>$check_box,
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_kids_params($record_id, $status)
    {

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set In-Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    public function get_event_kids_params($record_id, $status)
    {

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }


    public function get_gallery_params($record_id, $status,$image_url)
    {

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details" attr-url="'.$image_url.'"  attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set In-Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-url="'.$image_url.'" attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }
    

    

    public function get_eventtitle_params($record_id, $status,$image_url)
    {

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details" attr-url="'.$image_url.'"  attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set In-Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-url="'.$image_url.'" attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_cow_params($record_id, $status,$image_url,$rating)
    {

        if ($rating == 0) {
            $rating_text = 'Not rated';
        } elseif ($rating == 1) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 2) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 3) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 4) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bx-star"></i>';
        } elseif ($rating == 5) {
            $rating_text = '<i class="bx bxs-star" ></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i>';
        } else {
            //pass
        }

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details" attr-url="'.$image_url.'"  attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set In-Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-url="'.$image_url.'" attr-act=1 attr-id=' . $record_id . '  href="#" >Preview</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
            'rating_text'=>$rating_text
        );
        return $response;
    }
    
    public function get_chaperone_params($record_id, $status)
    {

        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=1 attr-id=' . $record_id . '  href="#" >Set Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >Set In-Active</a><br>'
                . '<a class="dropdown-item m actionbutton-details"  attr-act=1 attr-id=' . $record_id . '  href="#" >Details</a><br>'

                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    public function get_trainer_events_params($record_id,$trainer_verification_status,$trainer_id)
    {
        $status = $trainer_verification_status;
        if ($status == 0) {

            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton-act"  attr-act=1 attr-id=' . $record_id . '  href="#" >New Verification</a><br>'

                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">In-Active</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton-rate" data-toggle="modal" data-target="#rating-modal"  attr-act=1 attr-id=' . $record_id . '  href="#" >Rate Trainer</a><br>'
                . '<a class="dropdown-item m actionbutton-message" data-toggle="modal" data-target="#message-modal" attr-act=2 attr-id=' . $trainer_id . '  href="#" >Message Trainer</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_event_complete_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >View Rating</a><br>'
            . '<a class="dropdown-item m actionbutton"  attr-act=0 attr-id=' . $record_id . '  href="#" >View Income</a><br>'
            . '</div>'
            . '</div>';
        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Upcoming</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Ongoing</span>';
        } else if ($status == 3) {
            $unit_ui_display = '<span class=" badge-light btn-sm btn-block radius-30 centered-text">Complete</span>';
        }
        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function send_mail_client($compute_array)
    {
        $mail_sender = $_ENV['email_sender'];
        $mail_sender_credentials = $_ENV['email_cred'];
        if ($_ENV['DEV_MODE'] == 'true') {
            $titckttingemail = $_ENV['dev_ticket_email'];
            $ticket_email_sender = $_ENV['dev_ticket_email_name'];
            $ticket_email_receiver_name = $_ENV['dev_recever_name'];
            $ticket_email_receiver_email = $_ENV['dev_recever_email'];
            $bcced_name = $_ENV['dev_bcced_name'];
            $bcced_email = $_ENV['dev_bcced_email'];
        } else {
            $titckttingemail = $_ENV['ticket_email'];
            $ticket_email_sender = $_ENV['ticket_email_name'];
            $ticket_email_receiver_name = $_ENV['recever_name'];
            $ticket_email_receiver_email = $_ENV['recever_email'];
            $bcced_name = $_ENV['bcced_name'];
            $bcced_email = $_ENV['bcced_email'];
        }

        $is_private = false;

        if (isset($compute_array['is_private'])) {
            $is_private = $compute_array['is_private'];
        }

        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $mail_sender;
        $mail->Password = $mail_sender_credentials;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom($titckttingemail, $ticket_email_sender);
        $mail->addAddress($compute_array['email'], $compute_array['username']);
        if ($compute_array['subject'] != 'Account Verificaton') {
            if (!$is_private) {
                $mail->addBCC($bcced_email, $bcced_name);
            }
        } else {
            if (!$is_private) {
                $mail->addBCC($bcced_email, $bcced_name);
            }
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $compute_array['subject'];
        $mail->Body = $compute_array['message-html'];
        $mail->AltBody = $compute_array['message-text'];

        $mail->send();

        return true;
    }

    public function get_opportunity_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" href="/admin-vw-edit-opportunity/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function get_blog_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" href="/admin-vw-edit-blog/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    public function send_generic_mail($compute_array = null)
    {
        // $compute_array = array(
        //     'username' => '',
        //     'email' => $user_mail,
        //     'message-html' => $html_msg,
        //     'message-text' => $html_msg,
        //     'subject' => 'Registration OTP',
        // );

        $mail_sender = $_ENV['email_sender'];
        $mail_sender_credentials = $_ENV['email_cred'];
        $ticket_email_sender = $_ENV['email_sender_name'];

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $mail_sender;
            $mail->Password = $mail_sender_credentials;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom($mail_sender, $ticket_email_sender);

            $mail->addAddress($compute_array['email'], $compute_array['username']);

            //$mail->addBCC($ticket_email_receiver_email, $ticket_email_receiver_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $compute_array['subject'];
            $mail->Body = $compute_array['message-html'];
            $mail->AltBody = $compute_array['message-text'];

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function get_opp_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" href="/admin-vw-opp-app/?rec-id=' . $record_id . '&action=2" >More Details</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    public function get_faq_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
            . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=0 attr-id=' . $record_id . '  href="#" >De-Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=1 attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    
    public function get_guide_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
            . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=0 attr-id=' . $record_id . '  href="#" >De-Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=1 attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }
    
    
    public function get_hes_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
            . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=0 attr-id=' . $record_id . '  href="#" >De-Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=1 attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '<a class="dropdown-item m actionbutton-edit" attr-act="5" attr-id=' . $record_id . '  href="#" >Edit</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }

    
    public function get_testimonials_params($record_id, $status)
    {

        $dropdown = '<div class="btn-group">'
            . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
            . '<div class="dropdown-menu">'
            . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
            . '</div>'
            . '</div>';

        if ($status == 0) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act="1" attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '</div>'
                . '</div>';

            $unit_ui_display = '<span class=" badge-info btn-sm btn-block radius-30 centered-text">Ice-box</span>';
        } else if ($status == 1) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=0 attr-id=' . $record_id . '  href="#" >De-Activate</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-success btn-sm btn-block radius-30 centered-text">Active</span>';
        } else if ($status == 2) {
            $dropdown = '<div class="btn-group">'
                . '<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item m actionbutton" attr-act=1 attr-id=' . $record_id . '  href="#" >Activate</a><br>'
                . '</div>'
                . '</div>';
            $unit_ui_display = '<span class=" badge-danger btn-sm btn-block radius-30 centered-text">Closed</span>';
        }

        $response = array(
            'dropdown' => $dropdown,
            'unit_ui_display' => $unit_ui_display,
        );
        return $response;
    }
    
}
