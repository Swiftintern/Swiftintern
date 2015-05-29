<?php

/**
 * The admin controller which has highest privilege to manage the website
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Admin extends Users {

    public function index() {
        $this->seo(array(
            "title" => "Hire Interns | Register Company",
            "keywords" => "hire interns, post internship, register company, post training courses",
            "description" => "Hire Quality interns register with us and post internship, then further select from thousands of applicants available",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
        $opportunities = Opportunity::count();
        $view->set("opportunities", count($opportunities));
    }
    
    public function internships() {
        
    }

    public function competitions() {
        
    }

    public function trainings() {
        
    }

    public function editopportunities($id) {
        
    }

    public function createcrm($param) {
        
    }

    public function search() {
        
    }

    public function changeLayout() {
        $this->defaultLayout = "layouts/admin";
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

}