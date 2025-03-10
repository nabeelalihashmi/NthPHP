<?php

namespace Framework\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once "etc/email_config.php";

class Mail {

    private static $_instance = null;
    public  static $env = [];

    public function __construct() {
        self::$_instance = new PHPMailer(true);
    }
    public static function getInstance() {

        if (self::$_instance == null) {
            new self();
        }

        return self::$_instance;
    }

    public static function newInstance() {
        return new PHPMailer(true);
    }

    static function sendEmail($subject, $body, $to, $successMessage = 'Email has been sent.') {
        $mail = self::newInstance();


        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = Config::get('email.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = Config::get('email.username');
            $mail->Password   = Config::get('email.password');
            $mail->SMTPSecure = Config::get('email.security');
            $mail->Port       = Config::get('email.port');

            //Recipients
            $mail->setFrom(Config::get('email.username'), Config::get('email.sender'));

            if (is_array($to)) {
                foreach ($to as $recipient) {
                    if (isset($recipient[0]) && isset($recipient[1])) {
                        $mail->addAddress($recipient[0], $recipient[1]);
                    }
                }
            } else {
                $mail->addAddress($to[0], $to[1]);
            }


            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return ['success' => true, 'message' => $successMessage];
        } catch (Exception $e) {
            return ['success' => true, 'message' => "Error while sending email. {$mail->ErrorInfo}"];
        }
    }
}
