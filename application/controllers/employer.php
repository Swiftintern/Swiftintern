<?php

/**
 * Description of employer
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Employer extends Users {

    public function register() {
        $this->seo(array(
            "title" => "Hire Interns | Register Company",
            "keywords" => "hire interns, post internship, register company, post training courses",
            "description" => "Hire Quality interns register with us and post internship, then further select from thousands of applicants available",
            "view" => $this->getLayoutView()
        ));

        $view = $this->getActionView();

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
    public function index() {
        $this->changeLayout();
        $this->seo(array(
            "title" => "Dashboard",
            "keywords" => "dashboard",
            "description" => "Contains all realtime stats",
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
    }

}
