<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;

class Users extends Controller {
    
    private static $_template = array(
        "STUDENT_REGISTER" => "1",
        "INTERNSHIP_VERIFIED" => "2",
        "APPLICATION_SELECTED" => "3",
        "APPLICATION_REJECTED" => "4",
        "APPLICATION_INTERNSHIP" => "5"
    );
    
    protected function sendgrid() {
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/mail");

        if (!empty($parsed->mail->sendgrid) && !empty($parsed->mail->sendgrid->username)) {
            $sendgrid = new \SendGrid\SendGrid($parsed->mail->sendgrid->username, $parsed->mail->sendgrid->password);
            return $sendgrid;
        }
    }
    
    protected function mailgun() {
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/mail");

        if (!empty($parsed->mail->mailgun)) {
            $mailgun = new \Mailgun\Mailgun($parsed->mail->mailgun->key);
            return $mailgun;
        }
    }
    
    protected function notify($user, $type) {
        $sendgrid = $this->sendgrid();
        $mail = Message::first(array("id = ?"=> self::$_template[$type]));
        $email = new \SendGrid\Email();
        $email->addTo($user->email)
            ->setFrom('info@swiftintern.com')
            ->setFromName('Saud Akhtar')
            ->setSubject($mail->subject)
            ->setHtml($mail->body);
        $sendgrid->send($email);
    }

    public function logout() {
        $this->setUser(false);
        self::redirect("/home");
    }

    protected function LinkedIn($redirect = "") {
        $li = Framework\Registry::get("linkedin");
        if (!empty($redirect)) {
            $li->changeCallbackURL($redirect);
        }
        return $li;
    }

    public function noview() {
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
    }
}