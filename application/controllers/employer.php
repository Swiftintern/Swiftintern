<?php

/**
 * Description of employer
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
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

        $li = $this->LinkedIn("http://swiftintern.com/employer/register");
        $url = $li->getLoginUrl(array(
            LinkedIn::SCOPE_FULL_PROFILE,
            LinkedIn::SCOPE_EMAIL_ADDRESS,
            LinkedIn::SCOPE_COMPANY_ADMIN
        ));
        $view->set("url", $url);

        if (isset($_REQUEST['code'])) {
            $token = $li->getAccessToken($_REQUEST['code']);
        }

        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(first-name,last-name,positions,email-address,public-profile-url,picture-url)');

            //checks user exist and then logins
            if (!$this->access($info)) {
                //check if person is admin of any page
                $companies = $li->isCompanyAdmin('/companies');
                if (isset($companies["_total"]) && ($companies["_total"] > 0)) {
                    $orgs = array();
                    foreach ($companies["values"] as $key => $value) {
                        $org = Organization::first(array("linkedin_id = ?" => $value["id"]));
                        $company = $li->get("/companies/{$value['id']}:(id,name,website-url,description,industries,logo-url,employee-count-range,locations)");
                        if (!$org) {
                            //add all its company on our platform
                            $organization = new Organization(array(
                                "photo_id" => "",
                                "name" => $value["company"]["name"],
                                "address" => $company["locations"]["values"]["0"]["address"]["city"],
                                "phone" => "",
                                "country" => "",
                                "website" => $company["websiteUrl"],
                                "sector" => $company["industries"]["values"]["0"]["name"],
                                "number_employee" => $company["employeeCountRange"]["name"],
                                "type" => "company",
                                "about" => $company["description"],
                                "fbpage" => "",
                                "linkedin_id" => $value["company"]["id"],
                                "validity" => "1",
                                "updated" => ""
                            ));
                            $orgs[] = $organization->save();
                        }
                    }
                    $user = $this->newUser($info);
                    $info["user"] = $user;
                    $info["orgs"] = $orgs;
                    $this->newMember($info);
                    $this->createSession($user);
                } else {
                    $view->set("message", 'Please Register your company and be its admin on linkedin first....<a href="/support#register-on-linkedin-first">Read More</a>');
                }
            }
        }
    }

    protected function newMember($info = array()) {
        if ($info["orgs"]) {
            foreach ($info["orgs"] as $org) {
                $member = new Member(array(
                    "user_id" => $info["user"]->id,
                    "organization_id" => $org->id,
                    "designation" => "manager",
                    "authority" => "admin",
                    "validity" => "1",
                    "updated" => ""
                ));
                $member->save();
            }
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
        $view = $this->getActionView();
        $this->seo(array("title" => "Members", "keywords" => "dashboard", "description" => "Contains all realtime stats", "view" => $this->getLayoutView()));

        $session = Registry::get("session");
        $company = $session->get("employer")->organization;

        $employees = Member::all(array("organization_id = ?" => $company->id, "validity = ?" => true), array("user_id", "designation", "authority", "created"));
        $allmembers = array();
        foreach ($employees as $emp) {
            $user = User::first(array("id = ?" => $emp->user_id), array("name"));
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
        ));$view = $this->getActionView();
        
        $user = $this->getUser();
        $session = Registry::get("session");

        $inboxs = Message::all(array("to_user_id = ?" => $user->id,"validity = ?" => true), array("id", "from_user_id", "message", "created"), "id", "desc", 5, 1);
        $outboxs = Message::all(array("from_user_id = ?" => $user->id,"validity = ?" => true), array("id", "to_user_id", "message", "created"), "id", "desc", 5, 1);

        $allinbox = array();
        foreach ($inboxs as $in) {
            $user = User::first(array("id = ?" => $in->from_user_id), array("name"));

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
            $user = User::first(array("id = ?" => $out->to_user_id), array("name"));

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

    public function settings() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Edit Profile",
            "keywords" => "edit",
            "description" => "Edit your profile",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $user = $this->getUser();
        $view->set("errors", array());
    }
    
    public function reach() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Internship Reach",
            "keywords" => "reach",
            "description" => "opportunity internshipy reach posted on linkedin",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id, "type = ?" => "internship"));
        $view->set("opportunities", $opportunities);
    }
    
    public function followers() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Company Followers on linkedin",
            "keywords" => "followers",
            "description" => "Your company followers on linkedin",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
    }

    public function resources() {
        $this->seo(array(
            "title" => "Employer Resources",
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
