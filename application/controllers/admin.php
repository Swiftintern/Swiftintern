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
    
    /**
     * @readwrite
     */
    protected $_employer;

    public function index() {
        $this->seo(array(
            "title" => "Admin Panel",
            "keywords" => "admin",
            "description" => "admin",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        
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
        
        if($this->user->type != 'admin'){
            die('Not Admin');
        }

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