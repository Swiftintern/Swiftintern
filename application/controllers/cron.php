<?php

/**
 * Scheduler Class which executes daily and perfoms the initiated job
 * 
 * 
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class CRON extends Users {

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
    }

    public function index() {
        $this->newsletters();
    }

    protected function newsletters() {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $emails = array();$limit = 0;$count = 0;

        $newsletters = Newsletter::all(array("scheduled = ?" => $now));
        foreach ($newsletters as $newsletter) {
            $message = Message::first(array("id = ?"=>$newsletter->message_id));
            $users = User::all(array("type = ?" => $newsletter->user_group), array("email"));
            foreach ($users as $user) {
                $emails[$limit][] = $user->email;
                $count++;
                if($count == '999'){
                    $count=0;$limit++;
                }
            }
            
            for ($i=0;$i<=$limit;$i++){
                $this->notify(array(
                    "template" => "newsletter",
                    "delivery" => "mailgun",
                    "subject" => $message->subject,
                    "message" => $message,
                    "email" => implode(",", $emails[$i])
                ));
            }
        }
    }

    protected function notifications() {
        $yesterday = strftime("%Y-%m-%d", strtotime('-1 day'));
        $applications = Application::all(array("updated = ?" => $yesterday), array("id", "student_id", "opportunity_id", "status"));
        echo count($applications);
    }

    /**
     * @protected
     */
    public function _secure() {
        //echo php_sapi_name();
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            die('access is not permitted');
        }
    }

}
