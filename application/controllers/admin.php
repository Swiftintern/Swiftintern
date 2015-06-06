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
    
    public function update($model=NULL, $id=NULL) {
        $this->changeLayout();
        $this->seo(array("title" => "Update", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        $object = $model::first(array("id = ?" => $id));
        
        $vars = $object->columns;$array = array();
        foreach ($vars as $key => $value) {
            array_push($array, $key);
            $vars[$key] = htmlentities($object->$key);
        }
        if(RequestMethods::post("action")=="update"){
            foreach ($array as $field) {
                $object->$field = RequestMethods::post($field, $vars[$field]);
                $vars[$field] = htmlentities($object->$field);
            }
            $object->save();
            $view->set("success", true);
        }
        
        $view->set("vars", $vars);
        $view->set("array", $array);
        $view->set("model", $model);
        $view->set("id", $id);
    }
    
    public function crmTemplate() {
        $this->changeLayout();
        $this->seo(array("title" => "CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        if(RequestMethods::post("action") == "createCrmTemplate"){
            $body = RequestMethods::post("message");
            $subject = RequestMethods::post("subject");
            foreach ($body as $key => $value) {
                $msg = new Message(array("subject" => $subject[$key],"body" => $value));
                $msg->save();$message[] = $msg;
            }
            $crm = new CRM(array(
                "user_id" => $this->user->id,
                "title" => RequestMethods::post("title"),
                "first_message_id" => $message[0]->id,
                "second_message_id" => $message[1]->id
            ));
            $crm->save();
            $view->set("success", TRUE);
        }
    }
    
    public function crmManage() {
        $this->changeLayout();
        $this->seo(array("title" => "Manage CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $leads = Lead::all(array("user_id = ?" => $this->user->id), array("*"), "created", "desc", $limit, $page);
        $crms = CRM::all(array(), array("id","title"));
        
        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("leads", $leads);
        $view->set("crms", $crms);
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
