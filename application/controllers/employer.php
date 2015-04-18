<?php

/**
 * Description of employer
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Employer extends Users {

    /**
     * @readwrite
     */
    protected $_employer;

    public function register() {
        $this->seo(array(
            "title" => "Hire Interns | Register Company",
            "keywords" => "hire interns, post internship, register company, post training courses",
            "description" => "Hire Quality interns register with us and post internship, then further select from thousands of applicants available",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();

        $view->set("errors", array());

        if (RequestMethods::post("register")) {
            $user = new User(array(
                "first" => RequestMethods::post("first"),
                "last" => RequestMethods::post("last"),
                "email" => RequestMethods::post("email"),
                "password" => RequestMethods::post("password")
            ));

            if ($user->validate()) {
                $user->save();
                $this->_upload("photo", $user->id);
                $view->set("success", true);
            }

            $view->set("errors", $user->getErrors());
        }
    }

    /**
     * @before _secure
     */
    public function index() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Dashboard",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();

        $opportunities = Opportunity::all(
                        array("organization_id = ?" => $this->employer->organization->id), array("id")
        );

        $messages = Message::count(array("to_user_id = ?" => $this->user->id));
        $applicants = "0";
        foreach ($opportunities as $opportunity) {
            $applicants += Application::count(array("opportunity_id = ?" => $opportunity->id));
        }

        $view->set("opportunities", count($opportunities));
        $view->set("applicants", $applicants);
        $view->set("messages", $messages);
    }

    public function members() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Members",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $session = Registry::get("session");
        $company = $session->get("employer")->organization;
        $employees = Member::all(
                        array(
                    "organization_id = ?" => $company->id,
                    "validity = ?" => true), array("user_id", "designation", "authority", "created")
        );
        $allmembers = array();
        foreach ($employees as $emp) {
            $user = User::first(
                            array("id = ?" => $emp->user_id), array("name")
            );

            $allmembers[] = [
                "id" => $emp->id,
                "user_id" => $emp->user_id,
                "name" => $user->name,
                "designation" => $emp->designation,
                "authority" => $emp->authority,
                "created" => \Framework\StringMethods::datetime_to_text($emp->created)
            ];
        }

        $view->set("company", $company);
        $view->set("user", $this->getUser());
        $view->set("allmembers", \Framework\ArrayMethods::toObject($allmembers));
        $view->set("memberOf", $session->get("member"));
    }

    public function messages() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Messages",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $user = $this->getUser();
        $session = Registry::get("session");

        $inboxs = Message::all(
                        array(
                    "to_user_id = ?" => $user->id,
                    "validity = ?" => true
                        ), array("id", "from_user_id", "message", "created"), "id", "desc", 5, 1
        );
        $outboxs = Message::all(
                        array(
                    "from_user_id = ?" => $user->id,
                    "validity = ?" => true
                        ), array("id", "to_user_id", "message", "created"), "id", "desc", 5, 1
        );

        $allinbox = array();
        foreach ($inboxs as $in) {
            $user = User::first(
                            array("id = ?" => $in->from_user_id), array("name")
            );

            $allinbox[] = [
                "id" => $in->id,
                "from" => $user->name,
                "sender_id" => $in->from_user_id,
                "message" => $in->message,
                "received" => \Framework\StringMethods::datetime_to_text($in->created)
            ];
        }

        $alloutbox = array();
        foreach ($outboxs as $out) {
            $user = User::first(
                            array("id = ?" => $out->to_user_id), array("name")
            );

            $alloutbox[] = [
                "id" => $out->id,
                "to" => $user->name,
                "receiver_id" => $out->to_user_id,
                "message" => $out->message,
                "sent" => \Framework\StringMethods::datetime_to_text($out->created)
            ];
        }

        $view->set("user", $user);
        $view->set("alloutbox", \Framework\ArrayMethods::toObject($alloutbox));
        $view->set("allinbox", \Framework\ArrayMethods::toObject($allinbox));
    }

    public function profile_analytics() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Profile Analytics",
            "keywords" => "Analytics",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $startdate = strftime("%Y-%m-%d", strtotime('-1 week'));
        $enddate = strftime("%Y-%m-%d", time());
        $range = "{$startdate} - {$enddate}";
        $view->set("range", $range);
    }

    public function opportunity_analytics() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Opportuntiy Analytics",
            "keywords" => "Analytics",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $session = Registry::get("session");
        $view = $this->getActionView();
        $company = $session->get("employer")->organization;

        $opportunity = Opportunity::all(array("organization_id = ?" => $company->id), array("id", "title", "created"));
        $opportunities = array();
        foreach ($opportunity as $opp) {
            $find = Opportunity::first(array("id = ?" => $opp->id), array("id", "title", "created"));
            $opportunities[] = [
                "id" => $find->id,
                "title" => $find->title,
                "created" => $find->created
            ];
        }
        $view->set("opportunities", \Framework\ArrayMethods::toObject($opportunities));

        $startdate = strftime("%Y-%m-%d", strtotime('-1 week'));
        $enddate = strftime("%Y-%m-%d", time());
        $range = "{$startdate} - {$enddate}";
        $view->set("range", $range);
    }

    public function edit() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Edit Profile",
            "keywords" => "edit",
            "description" => "Edit your profile",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $user = $this->getUser();
        if (RequestMethods::post("save")) {
            echo 'here';
            $user = new User(array(
                "name" => RequestMethods::post("name"),
                "phone" => RequestMethods::post("phone")
            ));

            if ($user->validate()) {
                $user->save();
                $view->set("success", true);
            }

            $view->set("errors", $user->getErrors());
        }
    }

    public function edit_company() {
        $this->changeLayout();
        $session = Registry::get("session");
        $company = $session->get("employer")->organization;
        
        $this->seo(array(
            "title" => "Edit ".$company->name,
            "keywords" => "Analytics",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $view->set("company", $company);
    }

    public function integration() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Website Integration",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $session = Registry::get("session");
        $company = $session->get("employer")->organization;
        $view->set("company", $company);
    }

    public function faq() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Frequently asked Questions",
            "keywords" => "faq",
            "description" => "Frequently asked Questions",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
    }

    public function about() {
        $this->seo(array(
            "title" => "Why Hire Interns with Us?",
            "keywords" => "hire interns, post internship, company register",
            "description" => "Hire experienced interns who require very little, if any, training. But this dream conflicts with reality. How can organizations meet the needs of today and prepare the workforce of the future? One solution is to develop a quality internship program. We will assist you in doing just that.",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
    }

    public function changeLayout() {
        $this->defaultLayout = "layouts/employer";
        $this->setLayout();

        $session = Registry::get("session");
        $employer = $session->get("employer");
        $member = $session->get("member");

        $this->_employer = $employer;

        $this->getActionView()->set("employer", $employer);
        $this->getLayoutView()->set("employer", $employer);
        $this->getActionView()->set("member", $member);
        $this->getLayoutView()->set("member", $member);
    }

    public function switchOrganization($organization_id) {
        $session = Registry::get("session");
    }

}
