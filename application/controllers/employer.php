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
                    "last_login" => "",
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
                $view->set("message", 'Please Register your company and be its admin on linkedin first....<a href="https://business.linkedin.com/marketing-solutions/company-pages/get-started">Create Company Page</a>');
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
    public function index() {
        $this->seo(array(
            "title" => "Dashboard",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id), array("id"));
        $messages = "";
        $applicants = "0";
        foreach ($opportunities as $opportunity) {
            $applicants += Application::count(array("opportunity_id = ?" => $opportunity->id));
        }

        $view->set("opportunities", count($opportunities));
        $view->set("applicants", $applicants);
        $view->set("messages", $messages);
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
        $view = $this->getActionView();$conversations = array();        
        $user = $this->getUser();
        
        if (RequestMethods::post('action') == 'message') {
            $message = new Message(array(
                "subject" => $user->name. " sent you a message",
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
        
        $outbox = Conversation::all(array("property = ?" => "user", "property_id = ?" => $user->id),
                            array("user_id", "message_id", "created"), "id", "desc");
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
    public function postinternship() {
        $this->seo(array("title" => "Post Internship", "keywords" => "internshhip", "description" => "Your company internships on linkedin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "internship") {
            $opportunity = new Opportunity(array(
                "user_id" => $this->user->id, "organization_id" => $this->employer->organization->id,
                "title" => RequestMethods::post("title"),
                "details" => RequestMethods::post("details"),
                "eligibility" => RequestMethods::post("eligibility"),
                "category" => RequestMethods::post("category"),
                "duration" => RequestMethods::post("duration"),
                "location" => RequestMethods::post("location"),
                "type" => "internship",
                "last_date" => RequestMethods::post("last_date"),
                "payment" => RequestMethods::post("payment"),
                "payment_mode" => "offline", "application_type" => "resume", "type_id" => "", "is_active" => "1", "validity" => "0", "updated" => ""
            ));

            if ($opportunity->validate()) {
                $opportunity->save();
                if (RequestMethods::post("linkedin") == "1") {
                    $this->shareupdate(array(
                        "content" => array(
                            "title" => $opportunity->title,
                            "description" => substr(strip_tags($opportunity->details), 0, 150),
                            "submitted-url" => "http://swiftintern.com/internship/" . urlencode($opportunity->title) . "/" . $opportunity->id
                        ), "visibility" => array("code" => "anyone")
                            ), $opportunity);
                }
                self::redirect('/employer/internships');
            }

            $view->set("opportunity", $opportunity);
            $view->set("errors", $opportunity->getErrors());
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function internships() {
        $internships = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id, "type = ?" => "internship"), array("id", "title", "created"));
        $this->seo(array(
            "title" => "Manage Internships",
            "keywords" => "followers",
            "description" => "Your company followers on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $view->set("internships", $internships);
    }

    /**
     * @before _secure, changeLayout
     */
    public function internship($id = NULL) {
        if ($id == NULL) {
            self::redirect("/employer/internships");
        }
        $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id));
        $this->seo(array("title" => "Edit Internship", "keywords" => "edit", "description" => "edit", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "update") {
            $internship->title = RequestMethods::post("title");
            $internship->eligibility = RequestMethods::post("eligibility");
            $internship->last_date = RequestMethods::post("last_date");
            $internship->details = RequestMethods::post("details");
            $internship->payment = RequestMethods::post("payment");
            $internship->updated = date("Y-m-d H:i:s");

            $internship->save();
            $view->set("success", true);
            $view->set("errors", $internship->getErrors());
        }
        $view->set("internship", $internship);
    }

    /**
     * @before _secure, changeLayout
     */
    public function applicants($id = NULL) {
        if ($id == NULL) {
            self::redirect("/employer/internships");
        }
        $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id), array("id", "title"));
        $this->seo(array("title" => "Applications","keywords" => "Applications","description" => "Applications received on internship posted","view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $shortlisted = [];$selected = [];$applied = [];$applicants = [];$rejected = [];

        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 15);
        $count = Application::count(array("opportunity_id = ?" => $internship->id));
        $applications = Application::all(array("opportunity_id = ?" => $internship->id), array("id", "student_id", "property_id", "status", "created"), $order, $direction, $limit, $page);

        foreach ($applications as $application) {
            $student = Student::first(array("id = ?" => $application->student_id), array("user_id", "about"));
            $user = User::first(array("id = ?" => $student->user_id), array("name"));

            $applicant = \Framework\ArrayMethods::toObject(array(
                        "id" => $application->id,
                        "name" => $user->name,
                        "student_id" => $application->student_id,
                        "property_id" => $application->property_id,
                        "status" => $application->status,
                        "created" => $application->created
            ));
            $applicants[] = $applicant;
            switch ($application->status) {
                case "shortlisted":
                    $shortlisted[] = $applicant;
                    break;
                case "selected":
                    $selected[] = $applicant;
                    break;
                case "applied":
                    $applied[] = $applicant;
                    break;
                case "rejected":
                    $rejected[] = $applicant;
                    break;
            }
        }

        $view->set("internship", $internship);
        $view->set("count", $count);
        $view->set("shortlisted", Framework\ArrayMethods::toObject($shortlisted));
        $view->set("selected", Framework\ArrayMethods::toObject($selected));
        $view->set("applied", Framework\ArrayMethods::toObject($applied));
        $view->set("rejected", Framework\ArrayMethods::toObject($rejected));
        $view->set("applicants", Framework\ArrayMethods::toObject($applicants));
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
        if($organization_id != NULL){
            $organization = Organization::first(array("id = ?" => $organization_id),array("id", "name", "website", "type", "linkedin_id", "photo_id"));
            $opportunities = Opportunity::all(array("organization_id = ?" => $organization->id),array("id", "title"));
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

    public function switchorg($organization_id) {
        $this->noview();
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
