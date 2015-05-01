<?php

/**
 * The Controller to handle all employer pages
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

        if (isset($_REQUEST['code'])) {
            $token = $li->getAccessToken($_REQUEST['code']);
        } else {
            $url = $li->getLoginUrl(array(LinkedIn::SCOPE_FULL_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS, LinkedIn::SCOPE_COMPANY_ADMIN));
            $view->set("url", $url);
        }

        if ($li->hasAccessToken()) {
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
                    "phone" => $phone,
                    "password" => rand(100000, 99999999),
                    "access_token" => rand(100000, 99999999),
                    "type" => "employer",
                    "validity" => "1",
                    "last_ip" => $_SERVER['REMOTE_ADDR'],
                    "last_login" => "",
                    "updated" => ""
                ));
                $user->save();
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
                $view->set("message", 'Please Register your company and be its admin on linkedin first....<a href="/support#register-on-linkedin-first">Read More</a>');
            } else {
                $info["members"] = $members;
                $info["user"] = $user;
                $this->login($info);
                self::redirect("/employer");
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
                    "address" => $this->checkData($company["locations"]["values"]["0"]["address"]["city"]),
                    "phone" => "",
                    "country" => "",
                    "website" => $this->checkData($company["websiteUrl"]),
                    "sector" => $this->checkData($company["industries"]["values"]["0"]["name"]),
                    "number_employee" => $this->checkData($company["employeeCountRange"]["name"]),
                    "type" => "company",
                    "about" => $this->checkData($company["description"]),
                    "fbpage" => "",
                    "linkedin_id" => $company["id"],
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

        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id), array("id"));

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
        ));
        $view = $this->getActionView();

        $user = $this->getUser();
        $session = Registry::get("session");

        $inboxs = Message::all(array("to_user_id = ?" => $user->id, "validity = ?" => true), array("id", "from_user_id", "message", "created"), "id", "desc", 5, 1);
        $outboxs = Message::all(array("from_user_id = ?" => $user->id, "validity = ?" => true), array("id", "to_user_id", "message", "created"), "id", "desc", 5, 1);

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
        ));
        $view = $this->getActionView();

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
        ));$view = $this->getActionView();
        
        if (RequestMethods::get("action")) {
            $data = $this->followerstats(RequestMethods::post("startdate"),RequestMethods::post("startdate"));
        } else {
            $data = $this->followerstats(strftime("%Y-%m-%d", strtotime('-1 week')), strftime("%Y-%m-%d", strtotime('now')));
        }
        $view->set("data", $data);
        var_dump($data);
    }
    
    protected function followerstats($startdate, $enddate) {
        $li = Registry::get("linkedin");
        $totalFollowerCount = array();$time = array();$data = array();
        if ($li->hasAccessToken()) {
            $info = $li->get('/companies/3756293/historical-follow-statistics', array(
                "start-timestamp" => strtotime($startdate) * 1000,
                "time-granularity" => "day",
                "end-timestamp" => strtotime($enddate) * 1000
            ));
            foreach ($info["values"] as $key => $value) {
                array_push($totalFollowerCount, $value["totalFollowerCount"]);
                array_push($time, $value["time"]);
            }
            $data = array(
                "time" => $time,
                "totalFollowerCount" => $totalFollowerCount
            );
            return $data;
        }
    }

    public function postinternship() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Post Internship",
            "keywords" => "internshhip",
            "description" => "Your company internships on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
        if (RequestMethods::post("action") == "internship") {
            $opportunity = new Opportunity(array(
                "user_id" => $this->user->id,
                "organization_id" => $this->employer->organization->id,
                "title" => RequestMethods::post("title"),
                "details" => RequestMethods::post("details"),
                "eligibility" => RequestMethods::post("eligibility"),
                "category" => RequestMethods::post("category"),
                "duration" => RequestMethods::post("duration"),
                "location" => RequestMethods::post("location"),
                "type" => "internship",
                "last_date" => RequestMethods::post("last_date"),
                "payment" => RequestMethods::post("payment"),
                "payment_mode" => "offline",
                "application_type" => "resume",
                "type_id" => "",
                "is_active" => "1",
                "validity" => "0",
                "updated" => ""
            ));

            if ($opportunity->validate()) {
                $opportunity->save();
                if (RequestMethods::post("linkedin") == "1") {
                    $this->shareupdate(array(
                        "content" => array(
                            "title" => $opportunity->title,
                            "description" => substr(strip_tags($opportunity->details), 0, 150),
                            "submitted-url" => "http://swiftintern.com/internship/" . urlencode($opportunity->title) . "/" . $opportunity->id
                        ),
                        "visibility" => array(
                            "code" => "anyone"
                        )
                    ), $opportunity);
                }
                self::redirect('/employer/internships');
            }

            $view->set("errors", $opportunity->getErrors());
        }
    }

    /**
     * @before _secure
     */
    public function internships() {
        $this->changeLayout();
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
     * @before _secure
     */
    public function internship($id = 0) {
        $this->changeLayout();
        if ($id == 0) {
            $internship = Opportunity::first(array("organization_id = ? " => $this->employer->organization->id), array("id", "title", "eligibility", "last_date", "details", "payment"));
        } else {
            $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id), array("id", "title", "eligibility", "last_date", "details", "payment"));
        }
        $opportunities = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id), array("id", "title"));
        $this->seo(array(
            "title" => "Edit Internship",
            "keywords" => "followers",
            "description" => "Your company followers on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "edit") {
            $id = RequestMethods::post("id");
            header("Location: /employer/internship/{$id}");
            exit();
        }

        if (RequestMethods::post("action") == "update") {
            $internship->title = RequestMethods::post("title");
            $internship->eligibility = RequestMethods::post("eligibility");
            $internship->last_date = RequestMethods::post("last_date");
            $internship->details = RequestMethods::post("details");
            $internship->payment = RequestMethods::post("payment");
            $internship->updated = date("Y-m-d H:i:s");

            if ($internship->validate()) {
                $internship->save();
                $view->set("success", true);
            }
            $view->set("errors", $internship->getErrors());
        }
        $view->set("internship", $internship);
        $view->set("id", $id);
        $view->set("opportunities", $opportunities);
    }

    public function applicants($id = 1) {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Applications received on internship posted",
            "keywords" => "followers",
            "description" => "Your company followers on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        if ($id == 1) {
            $internship = Opportunity::first(array("organization_id = ? " => $this->employer->organization->id), array("id", "title"));
        } else {
            $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id), array("id", "title"));
        }
        $shortlisted = [];
        $selected = [];
        $applicants = [];

        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $applications = Application::all(array("opportunity_id = ?" => $internship->id), array("student_id", "property_id", "status", "created"), $order, $direction);

        foreach ($applications as $application) {
            $student = Student::first(array("id = ?" => $application->student_id), array("user_id", "about"));
            $user = User::first(array("id = ?" => $student->user_id), array("name"));
            $qualification = Qualification::first(array("student_id = ?" => $application->student_id), array("organization_id", "degree", "major", "passing_year"), "passing_year", "desc");
            $organization = Organization::first(array("id = ?" => $qualification->organization_id), array("name"));

            $applicant = \Framework\ArrayMethods::toObject(array(
                        "name" => $user->name,
                        "qualification" => $qualification,
                        "organization" => $organization,
                        "student_id" => $application->student_id,
                        "property_id" => $application->property_id,
                        "status" => $application->status,
                        "created" => $application->created
            ));
            $applicants[] = $applicant;
            switch ($application->status) {
                case "shortlist":
                    $shortlisted[] = $applicant;
                    break;
                case "selected":
                    $selected[] = $applicant;
                    break;
            }
        }

        $view->set("internship", $internship);
        $view->set("shortlisted", $shortlisted);
        $view->set("selected", $selected);
        $view->set("applicants", $applicants);
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
        //echo '<pre>', print_r($member), '</pre>';
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
}
