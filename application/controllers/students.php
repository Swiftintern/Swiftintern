<?php

/**
 * Students Method for profile, application, messages and Resume etc
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Students extends Users {

    /**
     * @readwrite
     */
    protected $_student;

    public function changeLayout() {
        $this->defaultLayout = "layouts/student";
        $this->setLayout();
        $session = Registry::get("session");
        $this->student = $session->get("student");
    }

    /**
     * @before _secure, changeLayout
     */
    public function index() {
        $profile = 0;

        $this->seo(array("title" => "Profile", "keywords" => "user profile", "description" => "Your Profile Page", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $session = Registry::get("session");
        $user = $this->user;
        $student = $session->get("student");

        $qualifications = Qualification::all(array("student_id = ?" => $student->id), array("id", "degree", "major", "organization_id", "gpa", "passing_year"));
        $works = Work::all(array("student_id = ?" => $student->id), array("id", "designation", "responsibility", "organization_id", "duration"));
        $socials = Social::all(array("user_id = ?" => $user->id), array("id", "social_platform", "link"));
        $resumes = Resume::all(array("student_id = ?" => $student->id), array("id", "type"));

        if (count($qualifications)) {
            ++$profile;
        }
        if (count($works)) {
            ++$profile;
        }
        if (!empty($student->about)) {
            ++$profile;
        }
        if (!empty($student->skills)) {
            ++$profile;
        }

        $view->set("student", $student);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
    }

    public function register() {
        $this->seo(array(
            "title" => "Get Internship | Student Register",
            "keywords" => "get internship, student register",
            "description" => "Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $li = $this->LinkedIn("http://swiftintern.com/students/register");
        if (isset($_REQUEST['code'])) {
            $li->getAccessToken($_REQUEST['code']);
        } else {
            $url = $li->getLoginUrl(array(LinkedIn::SCOPE_BASIC_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS));
            $view->set("url", $url);
        }

        if ($li->hasAccessToken()) {
            //$info = $li->get('/people/~:(phone-numbers,summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills)');
            $info = $li->get('/people/~:(summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url)');
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
            } else {
                $user = new User(array(
                    "name" => $info["firstName"] . " " . $info["lastName"],
                    "email" => $info["emailAddress"],
                    "phone" => $this->checkData($info["phoneNumbers"]["values"][0]["phoneNumber"]),
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

                //add student
                $skills = "";
                if ($info["skills"]["_total"] > 0) {
                    foreach ($info["skills"]["values"] as $key => $value) {
                        $skills .= $value["skill"]["name"];
                        $skills .= ",";
                    }
                }
                $student = new Student(array(
                    "user_id" => $user->id,
                    "about" => $this->checkData($info["summary"]),
                    "city" => $this->checkData($info["location"]["name"]),
                    "skills" => $skills,
                    "updated" => ""
                ));
                $student->save();
            }

            if (!$social) {
                $social = new Social(array(
                    "user_id" => $user->id,
                    "social_platform" => "linkedin",
                    "link" => $this->checkData($info["publicProfileUrl"])
                ));
                $social->save();
                $this->linkedinDetails($info, $student);
            }

            $info["user"] = $user;
            $this->login($info, $student);
            self::redirect("/students");
        }
    }

    protected function login($info, $student) {
        $this->user = $info["user"];
        $session = Registry::get("session");
        $session->set("student", $student);
    }

    protected function linkedinDetails($info, $student) {
        // Saving Education Info
        if ($info["educations"]["_total"] > 0) {
            foreach ($info["educations"]["values"] as $key => $value) {
                $organization = Organization::first(array("name = ?" => $value["schoolName"]), array("id"));
                if (!$organization) {
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
                    ));
                    $organization->save();
                }
                $qualification = new Qualification(array(
                    "student_id" => $student->id,
                    "organization_id" => $organization->id,
                    "degree" => $this->checkData($value["degree"]),
                    "major" => $this->checkData($value["fieldOfStudy"]),
                    "gpa" => "",
                    "passing_year" => $this->checkData($value["endDate"]["year"])
                ));
                $qualification->save();
            }
        }

        //Adding work experience
        if ($info["positions"]["_total"] > 0) {
            foreach ($info["positions"]["values"] as $key => $value) {
                $organization = Organization::first(array("name = ?" => $value["company"]["name"]), array("id"));
                if (!$organization) {
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
                        "linkedin_id" => $this->checkData($value["company"]["id"]),
                        "validity" => "1",
                        "updated" => ""
                    ));
                    $organization->save();
                }
                $work = new Work(array(
                    "student_id" => $student->id,
                    "organization_id" => $organization->id,
                    "duration" => $this->checkData($value["startDate"]["year"]),
                    "designation" => $this->checkData($value["title"]),
                    "responsibility" => $this->checkData($value["summary"])
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

        if (count($qualifications)) {
            ++$profile;
        }
        if (count($works)) {
            ++$profile;
        }
        if (!empty($student->about)) {
            ++$profile;
        }
        if (!empty($student->skills)) {
            ++$profile;
        }


        $view->set("student", $student);
        $view->set("user", $user);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
    }

    /**
     * @before _secure, changeLayout
     */
    public function messages() {
        $this->seo(array(
            "title" => "Messages",
            "keywords" => "user messages",
            "description" => "Your Inbox/Outbox",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
    }

    /**
     * @before _secure, changeLayout
     */
    public function applications() {
        $this->seo(array(
            "title" => "Applications",
            "keywords" => "student opportunity applications",
            "description" => "Your Application and its status",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $applications = Application::all(array("student_id = ?" => $this->student->id), array("id", "opportunity_id", "status", "created", "updated"));

        $view->set("applications", $applications);
    }

    /**
     * @before _secure, changeLayout
     */
    public function settings() {
        $this->seo(array(
            "title" => "Settings",
            "keywords" => "profile",
            "description" => "Updated Profile",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $session = Registry::get("session");
        $student = $session->get("student");

        $view->set("student", $student);
    }

    /**
     * @before _secure
     */
    public function qualification($id = NULL) {
        $session = Registry::get("session");
        $student = $session->get("student");

        if (isset($id)) {
            $qualification = Qualification::first(array("id = ?" => $id, "student_id = ?" => $student->id));
        } else {
            if (RequestMethods::post('action') == 'saveQual') {
                $institute = RequestMethods::post('institute');

                $organization = Organization::first(array("name = ?" => $institute), array("id"));
                if (!$organization) {
                    $organization = new Organization(array("photo_id" => "", "name" => $institute, "address" => "", "phone" => "", "country" => "", "website" => "", "sector" => "", "number_employee" => "", "type" => "institute", "about" => "", "fbpage" => "", "linkedin_id" => "", "validity" => "1", "updated" => ""));
                    $organization->save();
                }
                $qualification = new Qualification(array(
                    "student_id" => $student->id,
                    "organization_id" => $organization->id,
                    "degree" => RequestMethods::post('degree', ""),
                    "major" => RequestMethods::post('major', ""),
                    "gpa" => RequestMethods::post('gpa', ""),
                    "passing_year" => RequestMethods::post('passing_year', "")
                ));
                $qualification->save();
            }
        }
    }

}
