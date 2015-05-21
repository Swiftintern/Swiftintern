<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;

class Users extends Controller {
    
    protected $_sendgrid;
    private static $_template = array(
        "STUDENT_REGISTER" => "1",
        "INTERNSHIP_VERIFIED" => "2",
        "APPLICATION_SELECTED" => "3",
        "APPLICATION_REJECTED" => "4",
        "APPLICATION_INTERNSHIP" => 5
    );
    
    protected function mail() {
        $configuration = Registry::get("configuration");
        $configuration = $configuration->initialize();
        $parsed = $configuration->parse("configuration/sendgrid");

        if (!empty($parsed->sendgrid->default) && !empty($parsed->sendgrid->default->username)) {
            $sendgrid = new \SendGrid\SendGrid($parsed->sendgrid->default->username, $parsed->sendgrid->default->password);
            $this->_sendgrid = $sendgrid;
        }
    }
    
    /**
     * @before mail
     */
    protected function notify($user, $type) {
        $mail = Message::first(array("id = ?"=> self::$_template[$type]));
        $email = new \SendGrid\Email();
        $email->addTo($user->email)
            ->setFrom('info@swiftintern.com')
            ->setFromName('Saud Akhtar')
            ->setSubject($mail->subject)
            ->setHtml($mail->body);
        $this->_sendgrid->send($email);
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