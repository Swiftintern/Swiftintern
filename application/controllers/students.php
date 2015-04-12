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
    
    /**
     * Does three important things, first is retrieving the posted form data, second is checking each form field’s value
     * third thing it does is to create a new user row in the database
     */
    public function register() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Get Internship | Student Register");
        $seo->setKeywords("get internship, student register");
        $seo->setDescription("Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc");

        $this->getLayoutView()->set("seo", $seo);

        include APP_PATH . '/public/datalist.php';
        $view = $this->getActionView();

        $view->set("alldegrees", $alldegrees);
        $view->set("allmajors", $allmajors);
        $view->set("alllocations", $alllocations);

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
    public function profile() {
        $this->defaultLayout = "layouts/student";
        $this->setLayout();
        $seo = Registry::get("seo");

        $seo->setTitle("Profile");
        $seo->setKeywords("user profile");
        $seo->setDescription("Your Profile Page");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        $session = Registry::get("session");
        $user = $this->user;
        $student = $session->get("student");

        $qualifications = Qualification::all(
            array(
                "student_id = ?" => $student->id
            ),
            array("id", "degree", "major", "organization_id", "gpa", "passing_year")
        );
        
        $works = Work::all(
            array(
                "student_id = ?" => $student->id
            ),
            array("id", "designation", "responsibility", "organization_id", "duration")
        );

        $view->set("student", $student);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
    }

    /**
     * @before _secure
     */
    public function settings() {
        $view = $this->getActionView();
        $user = $this->getUser();

        if (RequestMethods::post("update")) {
            $user = new User(array(
                "first" => RequestMethods::post("first", $user->first),
                "last" => RequestMethods::post("last", $user->last),
                "email" => RequestMethods::post("email", $user->email),
                "password" => RequestMethods::post("password", $user->password)
            ));

            if ($user->validate()) {
                $user->save();
                $this->user = $user;
                $this->_upload("photo", $this->user->id);
                $view->set("success", true);
            }

            $view->set("errors", $user->getErrors());
        }
    }

    
    public function edit($id) {
        $errors = array();

        $user = User::first(array(
                    "id = ?" => $id
        ));

        if (RequestMethods::post("save")) {
            $user->first = RequestMethods::post("first");
            $user->last = RequestMethods::post("last");
            $user->email = RequestMethods::post("email");
            $user->password = RequestMethods::post("password");
            $user->live = (boolean) RequestMethods::post("live");
            $user->admin = (boolean) RequestMethods::post("admin");

            if ($user->validate()) {
                $user->save();
                $this->actionView->set("success", true);
            }

            $errors = $user->errors;
        }

        $this->actionView
                ->set("user", $user)
                ->set("errors", $errors);
    }

}