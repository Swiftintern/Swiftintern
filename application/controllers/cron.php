<?php

/**
 * Scheduler Class which executes daily and perfoms the initiated job
 * 
 * @author Faizan Ayubi
 */

class CRON extends Users {

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
    }

    public function index() {
        $this->_secure();
        $this->leads();
        $this->log("Leads Sent");
        $this->newsletters();
        $this->log("Newsletters Sent");
        $this->applicationStatus();
        $this->log("Application Status Sent");
        $this->newApplications();
        $this->log("newApplications Sent");
        $this->opportunityEnd();
        $this->log("Rejected All Applicants after last_date");
    }

    /**
     * Follow Leads, Send Further Message for completing Campaign
     */
    protected function leads() {
        $date = strftime("%Y-%m-%d", strtotime('-4 days'));
        $now = strftime("%Y-%m-%d", strtotime('now'));
        //using distinct so as to reduce db query for message and crm
        $leads = Lead::all(array("created = ?" => $date), array("DISTINCT crm_id"));
        foreach ($leads as $lead) {
            $crm = CRM::first(array("id = ?" => $lead->crm_id), array("second_message_id"));
            $message = Message::first(array("id = ?" => $crm->second_message_id));
            $lds = Lead::all(array("created = ?" => $date, "crm_id = ?" => $lead->crm_id));
            foreach ($lds as $ld) {
                $exist = User::first(array("email = ?" => $ld->email), array("id"));
                $user = User::first(array("id = ?" => $ld->user_id), array("id","name","email","phone"));
                if (!$exist) {
                    $ld->status = "SECOND_MESSAGE_SENT";
                    $this->notify(array(
                        "template" => "leadGeneration",
                        "subject" => $message->subject,
                        "message" => $message,
                        "user" => $user,
                        "from" => $user->name,
                        "emails" => array($ld->email)
                    ));
                } else {
                    $ld->status = "REGISTERED";
                }
                
                $ld->updated = $now;
                $ld->save();
            }
        }
    }

    /**
     * Sends Newsletters to User Group
     */
    protected function newsletters() {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $emails = array();$limit = 0;$count = 0;

        $newsletters = Newsletter::all(array("scheduled = ?" => $now));
        foreach ($newsletters as $newsletter) {
            $message = Message::first(array("id = ?" => $newsletter->message_id));
            $users = User::all(array("type = ?" => $newsletter->user_group), array("email"));
            foreach ($users as $user) {
                $emails[$limit][] = $user->email;$count++;
                if ($count == '999') {
                    $count = 0;$limit++;
                }
            }

            for ($i = 0; $i <= $limit; $i++) {
                $this->notify(array(
                    "template" => "newsletter",
                    "delivery" => "mailgun",
                    "subject" => $message->subject,
                    "message" => $message,
                    "track" => true,
                    "newsletter" => $newsletter,
                    "emails" => implode(",", $emails[$i])
                ));
            }
        }
    }
    
    /**
     * Rejects all applicants of an opportunity after 15 Days
     */
    protected function opportunityEnd() {
        $day15 = strftime("%Y-%m-%d", strtotime('-16 day'));
        $opportunities = Opportunity::all(array("last_date = ?" => $day15), array("id", "title"));
        foreach ($opportunities as $opportunity) {
            $applications = Application::all(array("opportunity_id = ?" => $opportunity->id, "status = ?" => "applied"));
            foreach ($applications as $application) {
                $application->status = "rejected";
                $application->save();
            }
        }
        
    }

    /**
     * Send Notifications to students for their application status
     */
    protected function applicationStatus() {
        $yesterday = strftime("%Y-%m-%d", strtotime('-1 day'));
        $applications = Application::all(array("updated LIKE ?" => "%{$yesterday}%"), array("id", "student_id", "opportunity_id", "status"));
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
     * Send Notifications to employers about number of students applied
     */
    protected function newApplications() {
        $created = strftime("%Y-%m-%d", strtotime('-1 day'));$emails = array();
        $applications = Application::all(array("created LIKE ?" => "%{$created}%"), array("DISTINCT opportunity_id"));
        foreach ($applications as $application) {
            $opportunity = Opportunity::first(array("id = ?" => $application->opportunity_id), array("title", "id", "organization_id"));
            $applicants = Application::count(array("created LIKE ?" => "%{$created}%", "opportunity_id = ?" => $application->opportunity_id));
            $members = Member::all(array("organization_id = ?" => $opportunity->organization_id),array("user_id"));
            foreach ($members as $member) {
                $mem = User::first(array("id = ?" => $member->user_id),array("email"));
                array_push($emails, $mem->email);
            }
            $this->notify(array(
                "template" => "employerApplicants",
                "subject" => "Internship Applications",
                "opportunity" => $opportunity,
                "applicants" => $applicants,
                "emails" => $emails
            ));
            $this->log(implode("", $emails));
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
