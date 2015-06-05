<?php

/**
 * The admin controller which has highest privilege to manage the website
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Admin extends Users {

    /**
     * @readwrite
     */
    protected $_employer;

    public function index() {
        $this->changeLayout();
        $this->seo(array("title" => "Admin Panel", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function search() {
        $this->changeLayout();
        $this->seo(array("title" => "Search", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $view->set("results", array());

        if (RequestMethods::get("action") == "search") {
            $model = RequestMethods::get("model");
            $key = RequestMethods::get("key");
            $value = RequestMethods::get("value");
            $r = new ReflectionClass(ucfirst($model));
            
            $object = $r->newInstanceWithoutConstructor()->all(array("{$key} = ?" => $value));
            $results = $object;
            //echo '<pre>', print_r($results), '</pre>';
            $view->set("results", $results);
        }
    }

    public function changeLayout() {
        $this->defaultLayout = "layouts/admin";
        $this->setLayout();

        if ($this->user->type != 'admin') {
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
