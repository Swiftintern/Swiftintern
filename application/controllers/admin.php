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

    /**
     * Method which sets data stats for admin dashboard
     * 
     * @before _secure
     */
    public function index() {
        $this->changeLayout();
        $this->seo(array("title" => "Admin Panel", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $users = User::count();
        $organizations = Organization::count();
        $opportunities = Opportunity::count();
        $applications = Application::count();
        $leads = Lead::count();
        $resumes = Resume::count();

        $view->set("users", $users);
        $view->set("organizations", $organizations);
        $view->set("opportunities", $opportunities);
        $view->set("applications", $applications);
        $view->set("leads", $leads);
        $view->set("resumes", $resumes);
    }

    /**
     * Seacrhs for data and returns result from db
     * 
     * @before _secure
     * @param type $model the model name
     * @param type $id model id
     */
    public function search($model = NULL, $id = NULL) {
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

    /**
     * Shows any data info
     * 
     * @before _secure
     * @param type $model the model to which shhow info
     * @param type $id the id of object model
     */
    public function info($model = NULL, $id = NULL) {
        $this->noview();
        $object = $model::first(array("id = ?" => $id));
        echo '<pre>', print_r($object), '</pre>';
    }

    /**
     * Updates any data provide with model and id
     * 
     * @before _secure
     * @param type $model the model object to be updated
     * @param type $id the id of object
     */
    public function update($model = NULL, $id = NULL) {
        $this->changeLayout();
        $this->seo(array("title" => "Update", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $object = $model::first(array("id = ?" => $id));

        $vars = $object->columns;
        $array = array();
        foreach ($vars as $key => $value) {
            array_push($array, $key);
            $vars[$key] = htmlentities($object->$key);
        }
        if (RequestMethods::post("action") == "update") {
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

    /**
     * @before _secure
     */
    public function stats() {
        $this->changeLayout();
        $this->seo(array("title" => "Stats", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        if (RequestMethods::get("action") == "getStats") {
            $startdate = RequestMethods::get("startdate");
            $enddate = RequestMethods::get("enddate");
            $property = ucfirst(RequestMethods::get("property"));
            $property_id = ucfirst(RequestMethods::get("property_id"));

            $diff = date_diff(date_create($startdate), date_create($enddate));
            for ($i = 0; $i < $diff->format("%a"); $i++) {
                $date = date('Y-m-d', strtotime($startdate . " +{$i} day"));
                $count = Stat::count(array("created = ?" => $date, "property = ?" => $property, "property_id = ?" => $property_id));
                $obj[] = array('y' => $date,'a' => $count);
            }
            $view->set("data", \Framework\ArrayMethods::toObject($obj));
        }
    }

    /**
     * @before _secure
     */
    public function data() {
        $this->changeLayout();
        $this->seo(array("title" => "Data Analysis", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        if (RequestMethods::get("action") == "dataAnalysis") {
            $startdate = RequestMethods::get("startdate");
            $enddate = RequestMethods::get("enddate");
            $model = ucfirst(RequestMethods::get("model"));

            $diff = date_diff(date_create($startdate), date_create($enddate));
            for ($i = 0; $i < $diff->format("%a"); $i++) {
                $date = date('Y-m-d', strtotime($startdate . " +{$i} day"));
                $count = $model::count(array("created LIKE ?" => "%{$date}%"));
                $obj[] = array('y' => $date,'a' => $count);
            }
            $view->set("data", \Framework\ArrayMethods::toObject($obj));
        }
    }

    /**
     * @before _secure
     */
    public function support() {
        $this->changeLayout();
        $this->seo(array("title" => "Support Tickets", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    /**
     * @before _secure
     */
    public function crmTemplate() {
        $this->changeLayout();
        $this->seo(array("title" => "CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        if (RequestMethods::post("action") == "createCrmTemplate") {
            $body = RequestMethods::post("message");
            $subject = RequestMethods::post("subject");
            foreach ($body as $key => $value) {
                $msg = new Message(array("subject" => $subject[$key], "body" => $value));
                $msg->save();
                $message[] = $msg;
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

    /**
     * @before _secure
     */
    public function crmLead() {
        $this->changeLayout();
        $this->seo(array("title" => "Lead Generation", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "leadGeneration") {
            if (RequestMethods::post("emails")) {
                $emails = explode(",", RequestMethods::post("emails"));
            } else {
                $emails = array();
            }
            if (!empty($_FILES['file']['name'])) {
                $tmpName = $_FILES['file']['tmp_name'];
                $csvAsArray = array_map('str_getcsv', file($tmpName));
                foreach ($csvAsArray as $key => $value) {
                    array_push($emails, $value[0]);
                }
            }

            foreach ($emails as $email) {
                $lead = new Lead(array(
                    "user_id" => $this->user->id,
                    "email" => $email,
                    "crm_id" => RequestMethods::post("crm_id"),
                    "status" => "FIRST_MESSAGE_SENT",
                    "validity" => "1",
                    "updated" => ""
                ));
                $lead->save();
            }
            $crm = CRM::first(array("id = ?" => $lead->crm_id), array("first_message_id"));
            $message = Message::first(array("id = ?" => $crm->first_message_id));
            $this->notify(array(
                "template" => "leadGeneration",
                "subject" => $message->subject,
                "message" => $message,
                "user" => $this->user,
                "from" => $this->user->name,
                "emails" => $emails
            ));
            $view->set("success", TRUE);
        }

        $crms = CRM::all(array(), array("id", "title"));
        $view->set("crms", $crms);
    }

    /**
     * @before _secure
     */
    public function crmManage() {
        $this->changeLayout();
        $this->seo(array("title" => "Manage CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $leads = Lead::all(array("user_id = ?" => $this->user->id), array("*"), "created", "desc", $limit, $page);

        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("leads", $leads);
    }

    /**
     * @before _secure
     */
    public function newsletterCreate() {
        $this->changeLayout();
        $this->seo(array("title" => "Create Newsletter", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "createNewsletter") {
            $message = new Message(array(
                "subject" => RequestMethods::post("subject"),
                "body" => RequestMethods::post("body")
            ));
            $message->save();

            $newsletter = new Newsletter(array(
                "message_id" => $message->id,
                "user_group" => RequestMethods::post("user_group"),
                "scheduled" => RequestMethods::post("scheduled")
            ));
            $newsletter->save();

            $view->set("success", TRUE);
        }
    }

    /**
     * @before _secure
     */
    public function newsletterManage() {
        $this->changeLayout();
        $this->seo(array("title" => "Manage Newsletter", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $newsletters = Newsletter::all(array(), array("*"), "created", "desc", $limit, $page);

        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("newsletters", $newsletters);
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
