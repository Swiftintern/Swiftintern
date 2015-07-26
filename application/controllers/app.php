<?php

/**
 * Main Controller to respond to all apps(android, ios, windows) request
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class App extends Users {

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
                        "meta_key = ?" => "app",
                        "meta_value = ?" => "placementpaper"
            ));
            if (!$meta) {
                $meta = new Meta(array(
                    "property" => "user",
                    "property_id" => $user->id,
                    "meta_key" => "app",
                    "meta_value" => "placementpaper"
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

}
