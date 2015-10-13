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

    /**
     * @before _secure, changeLayout
     */
    public function index() {
        $profile = 0;

        $this->seo(array("title" => "Profile", "keywords" => "user profile", "description" => "Your Profile Page", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $session = Registry::get("session");
        $user = $session->get("user");
        $student = $this->student;

        $qualifications = Qualification::all(array("student_id = ?" => $student->id), array("id", "degree", "major", "organization_id", "gpa", "passing_year"));
        $works = Work::all(array("student_id = ?" => $student->id), array("id", "designation", "responsibility", "organization_id", "duration"));
        $socials = Social::all(array("user_id = ?" => $user->id), array("id", "social_platform", "link"));
        $resumes = Resume::all(array("student_id = ?" => $student->id), array("id", "type"));

        if (count($qualifications)) ++$profile;
        if (count($works)) ++$profile;
        if ($student->about) ++$profile;
        if ($student->skills) ++$profile;

        $view->set("student", $student);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
    }

    public function register() {
        if ($this->user) {
            self::redirect("/students");
        }
        $this->seo(array(
            "title" => "Get Internship | Student Register",
            "keywords" => "get internship, student register",
            "description" => "Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $gClient = Registry::get("gClient");
        $li = $this->LinkedIn("http://swiftintern.com/students/register");
        $session = Registry::get("session");
        $loggedIn = false; $social_platform = null;

        if ($action = RequestMethods::get("action")) {
            if ($action == 'google') {
                $url = $gClient->createAuthUrl();
                $view->set("url", $url);
            } elseif ($action == 'linkedin') {
                $url = $li->getLoginUrl(array(LinkedIn::SCOPE_BASIC_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS));
                $view->set("url", $url);
            }
            $session->set("action", $action);
        }

        if (RequestMethods::get("redirectUrl")) {
            $session->set("redirectUrl", RequestMethods::get("redirectUrl"));
        }

        if (isset($_GET['code'])) { // Authorization successful
            if ($session->get('action') == 'google') {  // Google+ Login
                $gClient->authenticate($_GET['code']);    
            } elseif ($session->get('action') == 'linkedin') {  // or LinkedIn Login
                $li->getAccessToken($_REQUEST['code']);
            }
        }

        // Check Login token whether Google+ or LinkedIn
        if ($session->get('action') == 'google' && $gClient->getAccessToken()) {
            $tk = $gClient->verifyIdToken()->getAttributes();
            $obj = new Google_Service_Plus($gClient);
            $me = $obj->people->get('me');

            $loggedIn = true; $email = $tk['payload']['email'];
            $social_platform = "google_plus"; $link = $me->url;
        } elseif ($li->hasAccessToken()) {
            //$info = $li->get('/people/~:(phone-numbers,summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills)');
            $info = $li->get('/people/~:(summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url)');

            $loggedIn = true; $email = $info["emailAddress"];
            $social_platform = "linkedin"; $link = $info["publicProfileUrl"];
        }

        if ($loggedIn) {
            $session->erase('action');  // Unset the action to remove any error
            // find the user
            $user = $this->read(array(
                "model" => "user",
                "where" => array("email = ?" => $email)
            ));
            if ($user) {    // Found old user
                $social = $this->read(array(
                    "model" => "social",
                    "where" => array("user_id = ?" => $user->id, "social_platform = ?" => $social_platform)
                ));
                $student = Student::first(array("user_id = ?" => $user->id));
                $this->trackUser($user);
            } else {    // New User
                $social = false;
                switch ($social_platform) {
                    case 'google_plus':
                        $name = $me->displayName;
                        $phone = "";

                        // student details
                        $about = $me->aboutMe;
                        $city = $me->currentLocation;
                        $skills = $me->skills;

                        $object = $me;
                        break;
                    
                    case 'linkedin':
                        $name = $info["firstName"] . " " . $info["lastName"];
                        $phone = $info["phoneNumbers"]["values"][0]["phoneNumber"];

                        // student details
                        $about = $info["summary"];
                        $city = $info["location"]["name"];
                        $skills = "";
                        if ($info["skills"]["_total"] > 0) {
                            foreach ($info["skills"]["values"] as $key => $value) {
                                $skills .= $value["skill"]["name"];
                                $skills .= ",";
                            }
                        }

                        $object = $info;
                        break;
                }

                // save new user in db
                $user = new User(array(
                    "name" => $name, "email" => $email,
                    "phone" => $this->checkData($phone),
                    "password" => rand(100000, 99999999),
                    "access_token" => rand(100000, 99999999),
                    "type" => "student",
                    "validity" => "1",
                    "login_number" => "1",
                    "last_ip" => $_SERVER['REMOTE_ADDR'],
                    "last_login" => date('Y-m-d H:i:s'),
                    "updated" => ""
                ));
                $user->save();

                // email the new user
                $this->notify(array(
                    "template" => "studentRegister",
                    "subject" => "Getting Started on Swiftintern.com",
                    "user" => $user
                ));
                
                //add student
                $student = new Student(array(
                    "user_id" => $user->id,
                    "about" => $this->checkData($about),
                    "city" => $this->checkData($city),
                    "skills" => $this->checkData($skills),
                    "updated" => ""
                ));
                $student->save();
                $this->saveOptDetails($object, $student, $social_platform);
            }

            if (!$social) {
                $social = new Social(array(
                    "user_id" => $user->id,
                    "social_platform" => $social_platform,
                    "link" => $this->checkData($link)
                ));
                $social->save();
            }
            $this->login($user, $student);
            self::redirect("/students");
        }
    }

    protected function login($user, $student) {
        $this->setUser($user);
        Registry::get("session")->set("student", $student);
    }
    
    public function testLogin() {
        $this->JSONview();
        $view = $this->getActionView();
        
        $this->user = User::first(array("id = ?" => 31));
        $student = Student::first(array("user_id = ?" => "31"));
        
        $session = Registry::get("session");
        $session->set("student", $student);
        
        $view->set("user", $this->user);
        $view->set("student", $student);
    }
    
    public function testProfile() {
        $this->JSONview();
        $view = $this->getActionView();
        
        $view->set("user", $this->user);
    }

    protected function saveOptDetails($object, $student, $platform) {
        $isEdu = $isWork = false;
        if ($platform == 'google_plus') {
            $organizations = $object->getOrganizations();
            if (count($organizations) > 0) {
                foreach ($organizations as $org) {
                    $education = array(); $work = array();
                    switch ($org->type) {
                        case 'school':
                            $isEdu = true;
                            $education[] = array(
                                "school" => $org->name, "degree" => $org->title, "major" => "", "gpa" => "0.00", "passing_year" => $org->endDate
                            );
                            break;
                        
                        case 'work':
                            $isWork = true;
                            $work[] = array(
                                "company_name" => $org->name, "linkedin_id" => "", "duration" => $org->startDate . " - " . $org->endDate, "designation" => $org->title, "responsibility" => ""
                            );
                            break;
                    }
                }
            }
        } elseif($platform == 'linkedin') {
            if ($object["educations"]["_total"] > 0) {  // check for education/qual details
                $isEdu = true; $education = array();
                foreach ($object["educations"]["values"] as $key => $value) {
                    $education[] = array(
                        "school" => $value["schoolName"], "degree" => $value["degree"], "major" => $value["fieldOfStudy"], "gpa" => "0.00", "passing_year" => $value["endDate"]["year"]
                    );
                }
            }

            if ($object["positions"]["_total"] > 0) {   // check for work details
                $isWork = true; $work = array();
                foreach ($object["positions"]["_total"] as $key => $value) {
                    $work[] = array(
                        "company_name" => $value["company"]["name"], "linkedin_id" => $value["company"]["id"], "duration" => $value["startDate"]["year"], "designation" => $value["title"], "responsibility" => $value["summary"]
                    );
                }
            }
        }        

        // Saving Education Info
        if ($isEdu) {
            foreach ($education as $q) {
                $organization = Organization::first(array("name = ?" => $q["school"]), array("id"));
                if (!$organization) {
                    $organization = new Organization(array(
                        "photo_id" => "", "name" => $q["school"], "country" => "", "website" => "", "sector" => "", "type" => "institute", "account" => "basic", "about" => "", "fbpage" => "", "linkedin_id" => "", "validity" => "1", "updated" => ""
                    ));
                    $organization->save();
                }
                $newQual = new Qualification(array(
                    "student_id" => $student->id,
                    "organization_id" => $organization->id,
                    "degree" => $this->checkData($q["degree"]),
                    "major" => $this->checkData($q["major"]),
                    "gpa" => $this->checkData($q["gpa"]),
                    "passing_year" => $this->checkData($q["passing_year"])
                ));
                $newQual->save();
            }
        }

        //Adding work experience
        if ($isWork) {
            foreach ($work as $w) {
                $organization = Organization::first(array("name = ?" => $w["company_name"]), array("id"));
                if (!$organization) {
                    $organization = new Organization(array(
                        "photo_id" => "", "name" => $w["company_name"], "country" => "", "website" => "", "sector" => "", "type" => "company", "account" => "basic", "about" => "", "fbpage" => "", "linkedin_id" => $this->checkData($w["linkedin_id"]), "validity" => "1", "updated" => ""
                    ));
                    $organization->save();
                }
                $newWork = new Work(array(
                    "student_id" => $student->id,
                    "organization_id" => $organization->id,
                    "duration" => $this->checkData($w["duration"]),
                    "designation" => $this->checkData($w["designation"]),
                    "responsibility" => $this->checkData($w["responsibility"])
                ));
                $newWork->save();
            }
        }
        return;
    }

    public function toProfile($id) {
        $student = Student::first(array("user_id = ?" => $id), array("id"));
        self::redirect("/students/profile/".$student->id);
    }

    /**
     * @before _secure
     */
    public function profile($id) {
        if($this->user->type == "student") {
            die('Not Authorized');
        }
        $profile = 0;
        $view = $this->getActionView();

        $student = Student::first(array("id = ?" => $id));
        $user = User::first(array("id = ?" => $student->user_id), array("id", "name", "email", "type", "phone"));

        $this->seo(array("title" => $user->name, "keywords" => "user profile", "description" => "Your Profile Page", "view" => $this->getLayoutView()));

        $qualifications = Qualification::all(array("student_id = ?" => $student->id), array("id", "degree", "major", "organization_id", "gpa", "passing_year"));
        $works = Work::all(array("student_id = ?" => $student->id), array("id", "designation", "responsibility", "organization_id", "duration"));
        $socials = Social::all(array("user_id = ?" => $user->id), array("id", "social_platform", "link"));
        $resumes = Resume::all(array("student_id = ?" => $student->id), array("id", "type"));
        $participants = Participant::all(array("user_id = ?" => $user->id), array("DISTINCT test_id", "score", "created"));

        if (count($qualifications)) ++$profile;
        if (count($works)) ++$profile;
        if ($student->about) ++$profile;
        if ($student->skills) ++$profile;

        $view->set("student", $student);
        $view->set("user", $user);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
        $view->set("profile", $profile * 100 / 4);
        $view->set("resumes", $resumes);
        $view->set("socials", $socials);
        $view->set("participants", $participants);
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
        ));$view = $this->getActionView();
        
        $conversations = Conversation::all(array("user_id = ?" => $this->user->id));

        $view->set("conversations", $conversations);
    }

    /**
     * Students application to various internships
     * @before _secure, changeLayout
     */
    public function applications() {
        $this->seo(array(
            "title" => "Applications",
            "keywords" => "student opportunity applications",
            "description" => "Your Application and its status",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();

        if(RequestMethods::post("action") == "updateStatus"){
            $appli = Application::first(array("id = ?" => RequestMethods::post("application")));
            $appli->status = RequestMethods::post("status");
            $appli->updated = strftime("%Y-%m-%d %H:%M:%S", strtotime('now'));
            $appli->save();
        } else {
            $applications = Application::all(array("student_id = ?" => $this->student->id), array("id", "opportunity_id", "student_id", "status", "created", "updated"));
            $view->set("applications", $applications);
        }
    }

    /**
     * Edit Student Basic Details, Resumes, Social Links
     * @before _secure, changeLayout
     */
    public function settings() {
        $this->seo(array(
            "title" => "Settings",
            "keywords" => "profile",
            "description" => "Updated Profile",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        $view->set("student", $this->student);
        
        if (RequestMethods::post('action') == 'uploadResume') {
            $resume = new Resume(array(
                "student_id" => $this->student->id,
                "type" => "file",
                "resume" => $this->_upload("file"),
                "updated" => ""
            ));$resume->save();
            $view->set("success", true);
        }
        $resumes = Resume::all(array("student_id = ?" => $this->student->id));
        $view->set("resumes", $resumes);
        $session = Registry::get("session");

        if (RequestMethods::post('action') == 'saveUser') {
            $user = User::first(array("id = ?" => $this->user->id));
            $user->phone = RequestMethods::post('phone');
            $user->name = RequestMethods::post('name');
            $user->updated = date('Y-m-d H:i:s');
            $user->save();
            $this->setUser($user); 

            $view->set("success", true);
            $view->set("user", $user);
        }
        if (RequestMethods::post('action') == 'saveStudent') {
            $student = Student::first(array("id = ?" => $this->student->id));
            $student->city = RequestMethods::post("city");
            $student->about = RequestMethods::post("about");
            $student->skills = RequestMethods::post("skills");
            $student->updated = date('Y-m-d H:i:s');
            $student->save();
            $session->set("student", $student);
            
            $view->set("success", true);
            $view->set("student", $student);
        }
        if (RequestMethods::post('action') == 'saveSocial') {
            $social = new Social(array(
                "user_id" => $this->user->id,
                "social_platform" => RequestMethods::post('social_platform'),
                "link" => RequestMethods::post('link')
            ));
            $social->save();
            $view->set("success", true);
        }
        
    }
    
    /**
     * Common Delete Functions
     * @before _secure, changeLayout
     */
    public function delete() {
        $this->noview();
        if(RequestMethods::post("action") == "deletesocial"){
            $social = Social::first(array("id = ?" => RequestMethods::post("id")));
            if($this->user->id == $social->user_id){
                $social->delete();
                return TRUE;
            }
        }
        
        return FALSE;
    }

    /**
     * Edits and creates Qualification of Student
     * @before _secure, changeLayout
     * @param type $id qualification id of student
     */
    public function qualification($id = NULL) {
        $this->seo(array(
            "title" => "Qualification",
            "keywords" => "profile",
            "description" => "Updated Profile",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();

        $set = $this->saveQual($id);
        $view->set("success", $set['success']);
        $view->set("qualification", $set['qualification']);
        $view->set("organization", $set['organization']);
    }
    
    /**
     * Edit and created work details of Student
     * @before _secure, changeLayout
     * @param type $id the work id
     */
    public function work($id = NULL) {
        $this->seo(array(
            "title" => "Work",
            "keywords" => "profile",
            "description" => "Updated Profile",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();

        $set = $this->saveWork($id);
        $view->set("success", $set['success']);
        $view->set("work", $set['work']);
        $view->set("organization", $set['organization']);
    }

    /**
     * If $id is set then updates the work details else saves a new work in the database
     *
     * @return array
     */
    protected function saveWork($id = NULL) {
        if (isset($id)) {
            $work = Work::first(array("id = ?" => $id, "student_id = ?" => $this->student->id));
            // Since no qualification is found for the given $id so it is an invalid URL
            if (empty($work)) {
                self::redirect('/students');
            }
            $organization = Organization::first(array("id = ?" => $work->organization_id), array("id", "name"));
        } else {
            $work = new Work();
            $organization = new Organization();
        }

        if (RequestMethods::post('action') == 'saveWork') {
            $institute = RequestMethods::post('institute');
            $organization = Organization::first(array("name = ?" => $institute), array("id","name"));
            if (!$organization) {
                $organization = new Organization(array("photo_id" => "", "name" => $institute, "country" => "", "website" => "", "sector" => "", "type" => "company", "account" => "basic", "about" => "", "fbpage" => "", "linkedin_id" => "", "validity" => "1", "updated" => ""));
                $organization->save();
            }
            
            $work->organization_id = $organization->id;
            $work->student_id = $this->student->id;
            $work->duration = RequestMethods::post("duration", "");
            $work->designation = RequestMethods::post("designation", "");
            $work->responsibility = RequestMethods::post("responsibility", "");

            $work->save();
            return ['success' => true, 'work' => $work, 'organization' => $organization];
        }
        return ['success' => NULL, 'work' => $work, 'organization' => $organization];
    }

    /**
     * If $id is set then updates the Qualification details else saves a new qualification in the database
     *
     * @return array
     */
    protected function saveQual($id = NULL) {
        if (isset($id)) {
            $qualification = Qualification::first(array("id = ?" => $id, "student_id = ?" => $this->student->id));
            // Since no qualification is found for the given $id so it is an invalid URL
            if (empty($qualification)) {
                self::redirect('/students');
            }
            $organization = Organization::first(array("id = ?" => $qualification->organization_id), array("id", "name"));
        } else {
            $qualification = new Qualification();
            $organization = new Organization();
        }

        if (RequestMethods::post('action') == 'saveQual') {
            $institute = RequestMethods::post('institute');
            $organization = Organization::first(array("name = ?" => $institute), array("id","name"));
            if (!$organization) {
                $organization = new Organization(array("photo_id" => "", "name" => $institute, "country" => "", "website" => "", "sector" => "education", "type" => "institute", "account" => "basic", "about" => "", "fbpage" => "", "linkedin_id" => "", "validity" => "1", "updated" => ""));
                $organization->save();
            }
            
            $qualification->organization_id = $organization->id;
            $qualification->student_id = $this->student->id;
            $qualification->degree = RequestMethods::post('degree', "");
            $qualification->major = RequestMethods::post('major', "");
            $qualification->gpa = RequestMethods::post('gpa', "");
            $qualification->passing_year = RequestMethods::post('passing_year', "");

            $qualification->save();
            return ['success' => true, 'qualification' => $qualification, 'organization' => $organization];
        }
        return ['success' => NULL, 'qualification' => $qualification, 'organization' => $organization];
    }
    
    /**
     * Finds Resume for the Student and redirect to the location to view resume
     * @param type $student_id the student id
     */
    public function resume($student_id) {
        $this->noview();
        $resume = Resume::first(array("student_id = ?" => $student_id));
        if($resume){
            switch ($resume->type) {
                case "file":
                    header("Location: https://docs.google.com/gview?url=http://swiftintern.com/public/assets/uploads/files/{$resume->resume}");
                    break;
                case "text":
                    //echo $resume->resume;
                    break;
            }
        } else {
            echo 'Resume does not exist';
        }
    }
    
    /**
     * Changes the Standard Layout to Student Layout
     */
    public function changeLayout() {
        $this->defaultLayout = "layouts/student";
        $this->setLayout();
        $session = Registry::get("session");
        $this->student = $session->get("student");
        $this->user = $session->get('user');
    }

}
