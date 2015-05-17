<?php

/**
 * Contains all mail to be sent by various controllers
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Mail extends Users {
    
    protected $_sendgrid;
    
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
    
    protected function studentRegister($user) {
        $stuReg = '1';
        $mail = Message::first(array("id = ?"=> $stuReg));
        $email = new \SendGrid\Email();
        $email->addTo($user->email)
            ->setFrom('info@swiftintern.com')
            ->setFromName('Swiftintern Team')
            ->setSubject($mail->subject)
            ->setHtml($mail->body);
        $this->_sendgrid->send($email);
    }
}
