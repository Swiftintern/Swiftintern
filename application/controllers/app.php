<?php

/**
 * Main Controller to respond to all apps(android, ios, windows) request
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class App extends Users {

    function __construct($options=array())      {
        parent::__construct($options);

        $headers = getallheaders();
        if (isset($headers["acess-token"])) {
            $meta = Meta::first(array("property = ?" => "user","meta_key = ?" => "app", "meta_value = ?" => $headers["acess-token"]), array("property_id"));
            if ($meta) {
                $meta->meta_value;
                $user = User::first(array("id = ?" => $meta->property_id));
                $student = Student::first(array("user_id = ?" => $user->id));

                $this->login($user, $student);
            }
        }
    }

    public function index() {
        $this->JSONview();
        $view = $this->getActionView();
        $view->set("success", true);
    }

    public function student() {
        $this->JSONview();
        $view = $this->getActionView();
        if (RequestMethods::post("email")) {
            $user = $this->read(array(
                "model" => "user",
                "where" => array("email = ?" => RequestMethods::post("email"))
            ));
            if ($user) {
                $student = Student::first(array("user_id = ?" => $user->id));
                $this->trackUser($user);
            } else {
                $user = new User(array(
                    "name" => RequestMethods::post("name"),
                    "email" => RequestMethods::post("email"),
                    "phone" => RequestMethods::post("phone", ""),
                    "password" => rand(100000, 99999999),
                    "access_token" => rand(100000, 99999999),
                    "login_number" => "1",
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
                    "about" => "",
                    "city" => RequestMethods::post("city", ""),
                    "skills" => "",
                    "updated" => ""
                ));
                $student->save();
            }

            $meta = Meta::first(array(
                        "property = ?" => "user",
                        "property_id = ?" => $user->id,
                        "meta_key = ?" => "app"
            ));
            if (!$meta) {
                $meta = new Meta(array(
                    "property" => "user",
                    "property_id" => $user->id,
                    "meta_key" => "app",
                    "meta_value" => uniqid()
                ));
                $meta->save();
            }

            $this->user = $user;
            $session = Registry::get("session");
            $session->set("student", $student);

            $view->set("meta", $meta);
            $view->set("success", true);
        } else {
            $view->set("success", false);
        }
    }

    public function sponsored() {
        $this->JSONview();
        global $datetime;
        $sponsoreds = array();

        $order = RequestMethods::get("order", "id");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 1);

        $where = array(
            "start <= ?" => $datetime->format("Y-m-d"),
            "end >= ?" => $datetime->format("Y-m-d"),
            "validity = ?" => true,
            "is_active = ?" => true
        );
        $fields = array("opportunity_id");

        $sponsored = Sponsored::all($where, $fields, $order, $direction, $limit, $page);
        foreach ($sponsored as $sd) {
            $sponsoreds = Opportunity::all(array("id = ?" => $sd->opportunity_id), array("id", "title", "location", "last_date", "organization_id", "type"));
        }
        $this->getActionView()->set("sponsoreds", $sponsoreds);
    }

    public function upload() {
        $this->JSONview();
        $view = $this->getActionView();

        if (RequestMethods::post("title")) {
            $image = RequestMethods::post("title");
            $path = APP_PATH . "/public/assets/uploads/files/";
            $filename = uniqid() . ".pdf";

            if (file_put_contents($path.$filename,base64_decode($image))) {
                $resume = new Resume(array(
                    "student_id" => Registry::get("session")->get("student")->id,
                    "type" => "file",
                    "resume" => $filename,
                    "updated" => ""
                ));
                $resume->save();
                $view->set("success", true);
                $view->set("resume", $resume);
            }
        } else {
            $view->set("success", false);
        }
    }

    public function apply() {
        if (RequestMethods::post("action") == "internship") {
            $application = new Application(array(
                "student_id" => Registry::get("session")->get("student")->id,
                "opportunity_id" => RequestMethods::post("opportunity_id"),
                "property_id" => RequestMethods::post("resume_id"),
                "status" => "applied",
                "updated" => ""
            ));
            $application->save();
        }
    }

    public function test() {
        $this->JSONview();
        $view = $this->getActionView();
        //echo "<pre>", print_r(getallheaders()), "</pre>";

        $view->set("headers", getallheaders());
    }
}
