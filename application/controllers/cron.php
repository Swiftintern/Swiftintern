<?php

/**
 * Scheduler Class which executes daily and perfoms the initiated job
 * 
 * to dos
 * share experience of company
 * employer feedback and student feedback on swiftintern
 * 
 * @author Faizan Ayubi
 */
use Shared\PlacementPaper as PapersBot;
use Shared\Company as Company;

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
        $this->studentProfile();
        $this->log("Sent Mail to new Students to complete profile");
        $this->placementPapers();
        $this->log("Placement Papers bot");
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
                $exist = User::first(array("email = ?" => $ld->email), array("id", "name", "email", "phone"));
                if (!$exist) {
                    $ld->status = "SECOND_MESSAGE_SENT";
                    $this->notify(array(
                        "template" => "leadGeneration",
                        "subject" => $message->subject,
                        "message" => $message,
                        "user" => $exist,
                        "from" => $exist->name,
                        "emails" => array($ld->email)
                    ));
                } else {
                    $ld->status = "REGISTERED";
                }

                $ld->updated = $now;
                $ld->save();
            }
        }

        $second_leads = Lead::all(array("updated = ?" => $date));
        foreach ($second_leads as $second_lead) {
            $exist = User::first(array("email = ?" => $second_lead->email), array("id"));
            if ($exist) {
                $second_lead->status = "REGISTERED";
            } else {
                $second_lead->status = "NOT_REGISTERED";
            }
            $second_lead->updated = $now;
            $second_lead->save();
        }
    }

    /**
     * Sends Newsletters to User Group
     */
    protected function newsletters() {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $emails = array();
        $newsletters = Newsletter::all(array("scheduled = ?" => $now));
        foreach ($newsletters as $newsletter) {
            $message = Message::first(array("id = ?" => $newsletter->message_id));
            $users = User::all(array("type = ?" => $newsletter->user_group, "validity = ?" => 1), array("email"));
            foreach ($users as $user) {
                array_push($emails, $user->email);
            }

            $batches = array_chunk($emails, 1000);
            foreach ($batches as $batch) {
                $this->notify(array(
                    "template" => "newsletter",
                    "delivery" => "mailgun",
                    "subject" => $message->subject,
                    "message" => $message,
                    "track" => true,
                    "newsletter" => $newsletter,
                    "emails" => $batch
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
            $opportunity = Opportunity::first(array("id = ?" => $application->opportunity_id), array("title", "id", "organization_id", "type"));
            switch ($application->status) {
                case 'rejected':
                    $student = Student::first(array("id = ?" => $application->student_id), array("user_id"));
                    $user = User::first(array("id = ?" => $student->user_id), array("name"));
                    $this->notify(array(
                        "template" => "applicationRejected",
                        "subject" => $opportunity->title,
                        "user" => $user,
                        "opportunity" => $opportunity,
                        "organization" => Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "linkedin_id", "name"))
                    ));
                    break;
                case 'selected':
                    $student = Student::first(array("id = ?" => $application->student_id), array("user_id"));
                    $user = User::first(array("id = ?" => $student->user_id), array("name"));
                    $this->notify(array(
                        "template" => "applicationSelected",
                        "subject" => $opportunity->title,
                        "user" => $user,
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
        $created = strftime("%Y-%m-%d", strtotime('-1 day'));
        $emails = array();
        $applications = Application::all(array("created LIKE ?" => "%{$created}%"), array("DISTINCT opportunity_id"));
        foreach ($applications as $application) {
            $opportunity = Opportunity::first(array("id = ?" => $application->opportunity_id), array("title", "id", "organization_id"));
            $applicants = Application::count(array("created LIKE ?" => "%{$created}%", "opportunity_id = ?" => $application->opportunity_id));
            $members = Member::all(array("organization_id = ?" => $opportunity->organization_id), array("user_id"));
            foreach ($members as $member) {
                $mem = User::first(array("id = ?" => $member->user_id), array("email"));
                array_push($emails, $mem->email);
            }
            $this->notify(array(
                "template" => "employerApplicants",
                "subject" => "Internship Applications",
                "opportunity" => $opportunity,
                "applicants" => $applicants,
                "emails" => $emails
            ));
        }
    }

    protected function placementPapers() {
        $bot = new PapersBot();
        $companies = $bot->getCompaniesList();

        foreach ($companies as $id => $url) {
            $company = new Company(array($id => $url));
            $company->savePapers();
        }
    }

    /**
     * Mail Students to apply to internships those who have not applied after register after 7 days
     */
    protected function studentProfile() {
        $date = strftime("%Y-%m-%d", strtotime('-7 day'));
        $emails = array();
        $students = Student::all(array("created LIKE ?" => "%{$date}%"), array("id", "user_id"));
        foreach ($students as $student) {
            $application = Application::first(array("student_id = ?" => $student->id));
            if (!$application) {
                $user = User::first(array("id = ?" => $student->user_id), array("email"));
                array_push($emails, $user->email);
            }
        }

        if (!empty($emails)) {
            $this->notify(array(
                "template" => "studentProfileComplete",
                "subject" => "Keep Your Internship Search Going",
                "emails" => $emails
            ));
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
