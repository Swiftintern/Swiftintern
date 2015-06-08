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
        $this->_secure();
        $this->log("cron");
        $this->newsletters();
    }

    protected function leads() {
        $date = strftime("%Y-%m-%d", strtotime('-4 days'));
        $now = strftime("%Y-%m-%d", strtotime('now'));
        //using distinct so as to reduce db query for message and crm
        $leads = Lead::all(array("created = ?" => $date), array("DISTINCT crm_id"));
        foreach ($leads as $lead) {
            $crm = CRM::first(array("id = ?" => $lead->crm_id), array("second_message_id"));
            $message = Message::first(array("id = ?" => $crm->second_message_id));
            $lds = Lead::all(array("created = ?" => $date, "crm_id = ?" => $lead->crm_id), array("email", "user_id"));
            foreach ($lds as $ld) {
                $exist = User::first(array("email = ?" => $ld->email), array("id"));
                $user = User::first(array("id = ?" => $ld->user_id), array("id","name","email"));
                if (!$exist) {
                    $lead->status = "SECOND_MESSAGE_SENT";
                    $this->notify(array(
                        "template" => "leadGeneration",
                        "subject" => $message->subject,
                        "message" => $message,
                        "user" => $user,
                        "from" => $user->name,
                        "emails" => $ld->email
                    ));
                } else {
                    $lead->status = "REGISTERED";
                }
                
                $lead->updated = $now;
                $lead->save();
            }
        }
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
            $opportunity = Opportunity::first(array("id = ?" => $application->opportunity_id), array("title", "id", "organization_id"));
            switch ($application->status) {
                case 'rejected':
                    $this->notify(array(
                        "template" => "applicationRejected",
                        "subject" => $opportunity->title,
                        "user" => User::first(array("id = ?" => "31"), array("name")),
                        "opportunity" => $opportunity,
                        "organization" => Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "linkedin_id", "name"))
                    ));
                    break;
                case 'selected':
                    $this->notify(array(
                        "template" => "applicationSelected",
                        "subject" => $opportunity->title,
                        "user" => User::first(array("id = ?" => "31"), array("name")),
                        "opportunity" => $opportunity,
                        "application" => $application,
                        "organization" => Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "linkedin_id", "name"))
                    ));
                    break;
            }
        }
    }

    /**
     * @protected
     */
    public function _secure() {
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            die('access is not permitted');
        }
    }

}
