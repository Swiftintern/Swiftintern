<?php

/**
 * The Controller to handle all employer related request such as post internship stats etc
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

    /**
     * @before _secure, changeLayout
     */
    public function index($title = NULL, $id = NULL) {
        $this->seo(array("title" => "Dashboard","keywords" => "dashboard","description" => "Contains all realtime stats","view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (isset($id)) { $this->switchorg($id);}

        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id), array("id"));
        $messages = "";$applicants = "0";
        foreach ($opportunities as $opportunity) {
            $applicants += Application::count(array("opportunity_id = ?" => $opportunity->id));
        }

        $view->set("opportunities", count($opportunities));
        $view->set("applicants", $applicants);
        $view->set("messages", $messages);
    }

    public function register() {
        $this->seo(array(
            "title" => "Hire Interns | Register Company",
            "keywords" => "hire interns, post internship, register company, post training courses",
            "description" => "Hire Quality interns register with us and post internship, then further select from thousands of applicants available",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $li = $this->LinkedIn("http://swiftintern.com/employer/register");
        if (isset($_REQUEST['code'])) {
            $token = $li->getAccessToken($_REQUEST['code']);
        } else {
            $url = $li->getLoginUrl(array(LinkedIn::SCOPE_BASIC_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS, LinkedIn::SCOPE_COMPANY_ADMIN));
            $view->set("url", $url);
        }

        if ($li->hasAccessToken()) {
            //$info = $li->get('/people/~:(phone-numbers,first-name,last-name,positions,email-address,public-profile-url,picture-url)');
            $info = $li->get('/people/~:(first-name,last-name,positions,email-address,public-profile-url,picture-url)');

            $user = $this->read(array(
                "model" => "user",
                "where" => array("email = ?" => $info["emailAddress"])
            ));
            if ($user) {
                $social = $this->read(array(
                    "model" => "social",
                    "where" => array("user_id = ?" => $user->id, "social_platform = ?" => "linkedin")
                ));
                $this->trackUser($user);
            } else {
                $user = new User(array(
                    "name" => $info["firstName"] . " " . $info["lastName"],
                    "email" => $info["emailAddress"],
                    "phone" => $this->checkData($info["phoneNumbers"]["values"][0]["phoneNumber"]),
                    "password" => rand(100000, 99999999),
                    "access_token" => rand(100000, 99999999),
                    "type" => "employer",
                    "validity" => "1",
                    "last_ip" => $_SERVER['REMOTE_ADDR'],
                    "last_login" => date('Y-m-d H:i:s'),
                    "updated" => ""
                ));
                $user->save();

                $this->notify(array(
                    "template" => "employerRegister",
                    "subject" => "Getting Started on Swiftintern.com",
                    "user" => $user
                ));
            }
            if (!$social) {
                $social = new Social(array(
                    "user_id" => $user->id,
                    "social_platform" => "linkedin",
                    "link" => $info["publicProfileUrl"]
                ));
                $social->save();
            }

            $members = $this->member($social);
            if (!$members) {
                return $view->set("message", 'Please Register your company and be its admin on linkedin first....<a href="https://business.linkedin.com/marketing-solutions/company-pages/get-started">Create Company Page</a>');
            } else {
                $info["members"] = $members;
                $info["user"] = $user;
                $this->login($info);
            }
            if ($user->phone != "") {
                self::redirect("/employer");
            } else {
                self::redirect("/employer/settings");
            }
        }
    }

    protected function member($social) {
        $li = Registry::get("linkedin");
        $companies = $li->isCompanyAdmin('/companies');
        $membersof = array();

        if ($companies["_total"] == 0) {
            return FALSE;
        }
        //add all its company on our platform
        foreach ($companies["values"] as $key => $value) {
            $organization = Organization::first(array("linkedin_id = ?" => $value["id"]));
            if (!$organization) {
                $company = $li->get("/companies/{$value['id']}:(id,name,website-url,description,industries,logo-url,employee-count-range,locations)");
                $photo = new Photograph();
                $photoId = "";

                if (!empty($company["logoUrl"])) {
                    $photo->linkedinphoto($company["logoUrl"]);
                    $photo->save();
                    $photoId = $photo->id;
                }

                $organization = new Organization(array(
                    "photo_id" => $photoId,
                    "name" => $company["name"],
                    "country" => "",
                    "website" => $this->checkData($company["websiteUrl"]),
                    "sector" => $this->checkData($company["industries"]["values"]["0"]["name"]),
                    "type" => "company",
                    "account" => "basic",
                    "about" => $this->checkData($company["description"]),
                    "fbpage" => "",
                    "linkedin_id" => $this->checkData($company["id"]),
                    "validity" => "1",
                    "updated" => ""
                ));
                $organization->save();
            }

            $member = Member::first(array("user_id = ?" => $social->user_id, "organization_id = ?" => $organization->id));
            if (!$member) {
                $member = new Member(array(
                    "user_id" => $social->user_id,
                    "organization_id" => $organization->id,
                    "designation" => "Member",
                    "authority" => "admin",
                    "validity" => "1",
                    "updated" => ""
                ));
                $member->save();
            }
            $membersof[] = array(
                "id" => $member->id,
                "organization" => $organization,
                "designation" => $member->designation,
                "authority" => $member->authority
            );
        }
        return $membersof;
    }

    protected function login($info = array()) {
        $this->user = $info["user"];
        $session = Registry::get("session");
        $session->set("employer", Framework\ArrayMethods::toObject($info["members"][0]));
        $session->set("member", Framework\ArrayMethods::toObject($info["members"]));
    }

    /**
     * @before _secure, changeLayout
     */
    public function members() {
        $view = $this->getActionView();
        $allmembers = array();
        $session = Registry::get("session");
        $this->seo(array("title" => "Members", "keywords" => "dashboard", "description" => "Contains all realtime stats", "view" => $this->getLayoutView()));

        $employees = Member::all(array("organization_id = ?" => $this->employer->organization->id, "validity = ?" => true), array("user_id", "designation", "authority", "created"));
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

        $view->set("allmembers", \Framework\ArrayMethods::toObject($allmembers));
        $view->set("memberOf", $session->get("member"));
    }

    /**
     * @before _secure, changeLayout
     */
    public function messages() {
        $this->seo(array(
            "title" => "Messages",
            "keywords" => "dashboard",
            "description" => "Contains all Conversations",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
        $conversations = array();
        $user = $this->getUser();

        if (RequestMethods::post('action') == 'message') {
            $message = new Message(array(
                "subject" => $user->name . " sent you a message",
                "body" => RequestMethods::post('body')
            ));
            $message->save();

            $conversation = new Conversation(array(
                "user_id" => RequestMethods::post('user_id'),
                "property" => "user",
                "property_id" => $user->id,
                "message_id" => $message->id
            ));
            $conversation->save();
        }

        $outbox = Conversation::all(array("property = ?" => "user", "property_id = ?" => $user->id), array("user_id", "message_id", "created"), "id", "desc");
        $alloutbox = [];
        foreach ($outbox as $message) {
            $body = Message::first(array("id = ?" => $message->message_id), array("body"));
            $to = User::first(array("id = ?" => $message->user_id), array("name"));
            $alloutbox[] = [
                "receiver_id" => $message->user_id,
                "id" => $message->message_id,
                "to" => $to->name,
                "message" => $body->body,
                "sent" => \Framework\StringMethods::datetime_to_text($message->created)
            ];
        }

        $inbox = Conversation::all(array("user_id = ?" => $user->id, "property = ?" => "user"), array("created", "property_id", "message_id"), "id", "desc");
        $allinbox = [];
        foreach ($inbox as $message) {
            $body = Message::first(array("id = ?" => $message->message_id), array("body"));
            $from = User::first(array("id = ?" => $message->property_id), array("name"));
            $allinbox[] = [
                "sender_id" => $message->property_id,
                "id" => $message->message_id,
                "from" => $from->name,
                "message" => $body->body,
                "received" => \Framework\StringMethods::datetime_to_text($message->created)
            ];
        }
        $view->set("user", $user);
        $view->set("alloutbox", \Framework\ArrayMethods::toObject($alloutbox));
        $view->set("allinbox", \Framework\ArrayMethods::toObject($allinbox));
    }

    /**
     * @before _secure, changeLayout
     */
    public function settings() {
        $this->seo(array(
            "title" => "Settings",
            "keywords" => "edit",
            "description" => "Edit your profile",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        if (RequestMethods::post('action') == 'saveUser') {
            $user = User::first(array("id = ?" => $this->user->id));
            $user->phone = RequestMethods::post('phone');
            $user->name = RequestMethods::post('name');
            $user->save();
            $view->set("success", true);
            $view->set("user", $user);
        }

        if (RequestMethods::post("action") == "saveAccount") {
            $organization = Organization::first(array("id = ?" => $this->employer->organization->id));
            $organization->account = RequestMethods::post("account", "basic");
            $organization->save();
            $view->set("success", true);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function reach() {
        $this->seo(array("title" => "Internship Reach", "keywords" => "reach", "description" => "opportunity internshipy reach posted on linkedin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id, "type = ?" => "internship"), array("id,title"));
        $view->set("opportunities", $opportunities);
    }

    /**
     * @before _secure
     */
    public function reachstats($updatekey, $startdate, $enddate) {
        $li = Registry::get("linkedin");
        $session = Registry::get("session");
        $employer = $session->get("employer");
        $data = array();
        if ($li->hasAccessToken()) {
            $info = $li->get('/companies/' . $employer->organization->linkedin_id . '/historical-status-update-statistics', array(
                "start-timestamp" => strtotime($startdate) * 1000,
                "time-granularity" => "day",
                "end-timestamp" => strtotime($enddate) * 1000,
                "update-key" => $updatekey
            ));
            foreach ($info["values"] as $key => $value) {
                $t = strftime("%Y-%m-%d", $value["time"] / 1000);
                $data[$t] = $value["impressionCount"];
            }
            $chart = new PHPChart\Chart($data);
            $chart->drawBar(800, 400);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function followers() {
        $this->seo(array("title" => "Company Followers on linkedin", "keywords" => "followers", "description" => "Your company followers on linkedin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    /**
     * @before _secure
     */
    public function followerstats($startdate, $enddate) {
        $li = Registry::get("linkedin");
        $session = Registry::get("session");
        $employer = $session->get("employer");

        $totalFollowerCount = array();
        $time = array();
        $data = array();
        if ($li->hasAccessToken()) {
            $info = $li->get('/companies/' . $employer->organization->linkedin_id . '/historical-follow-statistics', array(
                "start-timestamp" => strtotime($startdate) * 1000,
                "time-granularity" => "day",
                "end-timestamp" => strtotime($enddate) * 1000
            ));
            foreach ($info["values"] as $key => $value) {
                array_push($totalFollowerCount, $value["totalFollowerCount"]);
                array_push($time, $value["time"]);
                $t = strftime("%Y-%m-%d", $value["time"] / 1000);
                $data[$t] = $value["totalFollowerCount"];
            }
            $chart = new PHPChart\Chart($data);
            $chart->drawBar(800, 400);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function resources() {
        $this->seo(array("title" => "Employer Resources", "keywords" => "faq", "description" => "Frequently asked Questions", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function widget($organization_id = NULL) {
        $this->willRenderLayoutView = false;
        $view = $this->getActionView();
        if ($organization_id != NULL) {
            $organization = Organization::first(array("id = ?" => $organization_id), array("id", "name", "website", "type", "linkedin_id", "photo_id"));
            $opportunities = Opportunity::all(array("organization_id = ?" => $organization->id), array("id", "title"));
            $view->set("organization", $organization);
            $view->set("opportunities", $opportunities);
        }
    }

    public function about() {
        $this->seo(array(
            "title" => "Why Hire Interns with Us?",
            "keywords" => "hire interns, post internship, company register",
            "description" => "Hire experienced interns who require very little, if any, training. But this dream conflicts with reality. How can organizations meet the needs of today and prepare the workforce of the future? One solution is to develop a quality internship program. We will assist you in doing just that.",
            "view" => $this->getLayoutView()
        ));
    }

    protected function shareupdate($opts, $meta) {
        $li = Registry::get("linkedin");
        if ($li->hasAccessToken()) {
            $info = $li->post('/companies/' . $this->employer->organization->linkedin_id . '/shares', $opts);
            foreach ($info as $key => $value) {
                $linkedin = new Meta(array(
                    "property" => "company_share_opportunity",
                    "property_id" => $meta->id,
                    "meta_key" => $key,
                    "meta_value" => $value
                ));
                $linkedin->save();
            }
            return $info;
        }
        return FALSE;
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
    
    protected function saveStudent($options) {
        $user = $this->read(array(
            "model" => "user",
            "where" => array("email = ?" => $options["email"])
        ));
        if ($user) {
            $student = Student::first(array("user_id = ?" => $user->id));
        } else {
            $user = new User(array(
                "name" => $options["name"],
                "email" => $options["email"],
                "phone" => $this->checkData($options["phone"]),
                "password" => rand(100000, 99999999),
                "access_token" => rand(100000, 99999999),
                "type" => "student",
                "validity" => "1",
                "last_ip" => $_SERVER['REMOTE_ADDR'],
                "last_login" => "1",
                "updated" => ""
            ));
            $user->save();
            $this->notify(array(
                "template" => "studentRegister",
                "subject" => "Getting Started on Swiftintern.com",
                "user" => $user
            ));
            $student = new Student(array(
                "user_id" => $user->id,
                "about" => $this->checkData($options["summary"]),
                "city" => $this->checkData($options["city"]),
                "skills" => $this->checkData($options["skills"]),
                "updated" => ""
            ));
            $student->save();
        }

        $this->user = $user;
        $session = Registry::get("session");
        $session->set("student", $student);
        return $student;
    }

    protected function switchorg($organization_id) {
        $session = Registry::get("session");
        $member = $session->get("member");

        foreach ($member as $mem) {
            if ($organization_id == $mem->organization->id) {
                $session->set("employer", $mem);
                self::redirect("/employer");
            }
        }
    }

    /**
     * @protected
     */
    public function _secure() {
        $user = $this->getUser();
        $session = Registry::get("session");
        $member = $session->get("member");

        if (!$user || !$member) {
            header("Location: /home");
            exit();
        }
    }

}
