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
        $this->log("cron");
        $this->_secure();
        $this->newsletters();
    }

    protected function newsletters() {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $emails = array();
        $limit = 0;
        $count = 0;

        $newsletters = Newsletter::all(array("scheduled = ?" => $now));
        foreach ($newsletters as $newsletter) {
            $message = Message::first(array("id = ?" => $newsletter->message_id));
            $users = User::all(array("type = ?" => $newsletter->user_group), array("email"));
            foreach ($users as $user) {
                $emails[$limit][] = $user->email;
                $count++;
                if ($count == '999') {
                    $count = 0;
                    $limit++;
                }
            }

            for ($i = 0; $i <= $limit; $i++) {
                $this->notify(array(
                    "template" => "newsletter",
                    "delivery" => "mailgun",
                    "subject" => $message->subject,
                    "message" => $message,
                    "newsletter" => $newsletter,
                    "emails" => implode(",", $emails[$i])
                ));
            }
        }
    }

    protected function notifications() {
        $yesterday = strftime("%Y-%m-%d", strtotime('-1 day'));
        $applications = Application::all(array("updated = ?" => $yesterday), array("id", "student_id", "opportunity_id", "status"));
        foreach ($applications as $application) {
            $opportunity = Opportunity::first(array("id = ?"=> $application->opportunity_id),array("title","id","organization_id"));
            switch ($application->status) {
                case 'rejected':
                    $this->notify(array(
                        "template" => "applicationRejected",
                        "subject" => $opportunity->title,
                        "user" => User::first(array("id = ?" => "31"),array("name")),
                        "opportunity" => $opportunity,
                        "organization" => Organization::first(array("id = ?"=> $opportunity->organization_id),array("id","linkedin_id","name"))
                    ));
                    break;
                case 'selected':
                    $this->notify(array(
                        "template" => "applicationSelected",
                        "subject" => $opportunity->title,
                        "user" => User::first(array("id = ?" => "31"),array("name")),
                        "opportunity" => $opportunity,
                        "application" => $application,
                        "organization" => Organization::first(array("id = ?"=> $opportunity->organization_id),array("id","linkedin_id","name"))
                    ));
                    break;
            }
        }
    }

    /**
     * @protected
     */
    public function _secure() {
        echo php_sapi_name();
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            die('access is not permitted');
        }
    }

}
