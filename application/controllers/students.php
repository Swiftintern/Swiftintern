<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Students extends Users {

    public function changeLayout() {
        $this->defaultLayout = "layouts/student";
        $this->setLayout();
    }

    /**
     * @before _secure
     */
    public function index() {
        $this->changeLayout();$profile = 0;

        $this->seo(array("title" => "Profile", "keywords" => "user profile", "description" => "Your Profile Page", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $session = Registry::get("session");
        $user = $this->user;
        $student = $session->get("student");

        $qualifications = Qualification::all(array("student_id = ?" => $student->id), array("id", "degree", "major", "organization_id", "gpa", "passing_year"));
        $works = Work::all(array("student_id = ?" => $student->id), array("id", "designation", "responsibility", "organization_id", "duration"));
        $socials = Social::all(array("user_id = ?" => $user->id), array("id", "social_platform", "link"));
        $resumes = Resume::all(array("student_id = ?" => $student->id), array("id", "type"));

        if (count($qualifications)) {++$profile;}
        if (count($works)) {++$profile;}
        if (!empty($student->about)) {++$profile;}
        if (!empty($student->skills)) {++$profile;}

        $view->set("student", $student);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
    }

    public function register() {
        $li = Framework\Registry::get("linkedin");
        $li->changeCallbackURL("http://swiftintern.com/students/register");
        $url = $li->getLoginUrl(array(LinkedIn::SCOPE_FULL_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS, LinkedIn::SCOPE_CONTACT_INFO));

        $this->seo(array(
            "title" => "Get Internship | Student Register",
            "keywords" => "get internship, student register",
            "description" => "Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();
        $view->set("url", $url);

        if (isset($_REQUEST['code'])) {
            $token = $li->getAccessToken($_REQUEST['code']);
        }

        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills)');

            $user = $this->read(array(
                "model" => "user",
                "where" => array("email = ?" => $info["emailAddress"])
            ));

            if ($user) {
                $social = $this->read(array(
                    "model" => "social",
                    "where" => array("user_id = ?" => $user->id, "social_platform = ?" => "linkedin")
                ));
                $student = Student::first(array("user_id = ?" => $user->id));
                if(!$social){
                    $this->linkedinDetails($info, $student);
                }
                
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

                //add student
                if (isset($info["location"]["name"])) {
                    $city = $info["location"]["name"];
                } else {
                    $city = "";
                }
                $skills = "";
                if ($info["skills"]["_total"] > 0) {
                    foreach ($info["skills"]["values"] as $key => $value) {
                        $skills .= $value["skill"]["name"];
                        $skills .= ",";
                    }
                }
                $student = new Student(array(
                    "user_id" => $user->id,
                    "about" => $info["summary"],
                    "city" => $city,
                    "skills" => $skills,
                    "updated" => ""
                ));
                $student->save();
                
                $social = new Social(array(
                    "user_id" => $user->id,
                    "social_platform" => "linkedin",
                    "link" => $info["publicProfileUrl"]
                ));
                $social->save();
                
                $this->linkedinDetails($info, $student);
            }

            $info["user"] = $user;
            $this->login($info);
            self::redirect("/students");
        }
    }
    
    protected function linkedinDetails($info = array(), $student) {
        // Saving Education Info
        if ($info["educations"]["_total"] > 0) {
            foreach ($info["educations"]["values"] as $key => $value) {
                $org = Organization::first(array("name = ?" => $value["schoolName"]), array("id"));
                if ($org) { $orgId = $org->id;} 
                else {
                    $organization = new Organization(array(
                        "photo_id" => "",
                        "name" => $value["schoolName"],
                        "address" => "",
                        "phone" => "",
                        "country" => "",
                        "website" => "",
                        "sector" => "",
                        "number_employee" => "",
                        "type" => "institute",
                        "about" => "",
                        "fbpage" => "",
                        "linkedin_id" => "",
                        "validity" => "1",
                        "updated" => ""
                    ));$organization->save();
                    $orgId = $organization->id;
                }
                $qualification = new Qualification(array(
                    "student_id" => $student->id,
                    "organization_id" => $orgId,
                    "degree" => $value["degree"],
                    "major" => $value["fieldOfStudy"],
                    "gpa" => "",
                    "passing_year" => $value["endDate"]["year"]
                ));
                $qualification->save();
            }
        }
        
        //Adding work experience
        if ($info["positions"]["_total"] > 0) {
            foreach ($info["positions"]["values"] as $key => $value) {
                $org = Organization::first(array("name = ?" => $value["company"]["name"]), array("id"));
                if ($org) { $orgId = $org->id;}
                else {
                    $organization = new Organization(array(
                        "photo_id" => "",
                        "name" => $value["company"]["name"],
                        "address" => "",
                        "phone" => "",
                        "country" => "",
                        "website" => "",
                        "sector" => "",
                        "number_employee" => "",
                        "type" => "company",
                        "about" => "",
                        "fbpage" => "",
                        "linkedin_id" => "",
                        "validity" => "1",
                        "updated" => ""
                    ));$organization->save();
                    $orgId = $organization->id;
                }
                $work = new Work(array(
                    "student_id" => $student->id,
                    "organization_id" => $orgId,
                    "duration" => "from " . $value["startDate"]["year"],
                    "designation" => $value["title"],
                    "responsibility" => $value["summary"]
                ));
                $work->save();
            }
        }
    }

    /**
     * @before _secure
     */
    public function profile($id) {
        $profile = 0;
        $view = $this->getActionView();

        $student = Student::first(array("id = ?" => $id));
        $user = User::first(array("id = ?" => $student->user_id), array("id", "name", "email", "type"));

        $this->seo(array("title" => $user->name, "keywords" => "user profile", "description" => "Your Profile Page", "view" => $this->getLayoutView()));

        $qualifications = Qualification::all(array("student_id = ?" => $student->id), array("id", "degree", "major", "organization_id", "gpa", "passing_year"));
        $works = Work::all(array("student_id = ?" => $student->id), array("id", "designation", "responsibility", "organization_id", "duration"));
        $socials = Social::all(array("user_id = ?" => $user->id), array("id", "social_platform", "link"));
        $resumes = Resume::all(array("student_id = ?" => $student->id), array("id", "type"));

        if (count($qualifications)) {++$profile;}
        if (count($works)) {++$profile;}
        if (!empty($student->about)) {++$profile;}
        if (!empty($student->skills)) {++$profile;}


        $view->set("student", $student);
        $view->set("user", $user);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
    }

    /**
     * @before _secure
     */
    public function messages() {
        $this->changeLayout();
        $seo = Registry::get("seo");

        $seo->setTitle("Messages");
        $seo->setKeywords("user messages");
        $seo->setDescription("Your Inbox/Outbox");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();

        $user = $this->user;

        $inboxs = Message::all(array("to_user_id = ?" => $user->id, "validity = ?" => true), array("id", "from_user_id", "message", "created"));

        $outboxs = Message::all(array("from_user_id = ?" => $user->id, "validity = ?" => true), array("id", "to_user_id", "message", "created"));

        $view->set("inboxs", $inboxs);
        $view->set("outboxs", $outboxs);
    }

    /**
     * @before _secure
     */
    public function applications() {
        $this->defaultLayout = "layouts/student";
        $this->setLayout();
        $seo = Registry::get("seo");

        $seo->setTitle("Applications");
        $seo->setKeywords("student opportunity applications");
        $seo->setDescription("Your Application and its status");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();

        $session = Registry::get("session");
        $student = $session->get("student");

        $applications = Application::all(array("student_id = ?" => $student->id), array("id", "opportunity_id", "status", "created", "updated"));

        $view->set("applications", $applications);
    }

}
