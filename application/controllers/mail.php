<?php

/**
 * Contains all mail to be sent by various controllers
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;

class Mail extends Users {
    
    protected $_sendgrid;
    private static $_template = array(
        "STUDENT_REGISTER" => "1",
        "INTERNSHIP_VERIFIED" => "2",
        "APPLICATION_SELECTED" => "3",
        "APPLICATION_REJECTED" => "4",
        "APPLICATION_INTERNSHIP" => 5
    );
    
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->noview();
        
        $configuration = Registry::get("configuration");
        $configuration = $configuration->initialize();
        $parsed = $configuration->parse("configuration/sendgrid");

        if (!empty($parsed->sendgrid->default) && !empty($parsed->sendgrid->default->username)) {
            $sendgrid = new \SendGrid\SendGrid($parsed->sendgrid->default->username, $parsed->sendgrid->default->password);
            $this->_sendgrid = $sendgrid;
        }
        
    }
    
    protected function test() {
        $stuReg = '1';
        $mail = Message::first(array("id = ?"=> $stuReg));
        $email = new \SendGrid\Email();
        $email->addTo('indianayubi@gmail.com')
            ->setFrom('info@swiftintern.com')
            ->setFromName('Swiftintern Team')
            ->setSubject($mail->subject)
            ->setHtml($mail->body);
        $this->_sendgrid->send($email);
    }
    
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
}
