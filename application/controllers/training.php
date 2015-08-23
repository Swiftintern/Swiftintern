<?php

/**
 * Description of training
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Training extends Employer {

    public function index($category = NULL, $location = NULL) {
        $this->seo(array(
            "title" => "Training for College Students",
            "keywords" => "college student engineering training",
            "description" => "Apply to the best training program by experts in the industry to improve your skills.",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
        $certificates = Certificate::count();

        $trainings = Opportunity::all(array("type = ?" => "training", "validity = ?" => 1));
        $view->set("trainings", $trainings);
        $view->set("certificates", $certificates);
    }

    public function details($title, $id) {
        global $datetime;
        $view = $this->getActionView();
        $session = Registry::get("session");
        $student = $session->get("student");

        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name", "photo_id"));
        if ($student) {
            $application = Application::first(array("student_id = ?" => $student->id, "opportunity_id = ?" => $id));
            $view->set("application", $application);
        }
        $this->enroll($opportunity, $student);
        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "photo" => APP . "thumbnails/" . $organization->photo_id,
            "view" => $this->getLayoutView()
        ));

        $view->set("enddate", $datetime->format("Y-m-d"));
        $view->set("opportunity", $opportunity);
        $view->set("organization", $organization);
    }

    protected function enroll($opportunity, $student = NULL) {
        if (RequestMethods::post("quickApply") == "quickApply") {
            $options = array(
                "email" => RequestMethods::post("email"),
                "name" => RequestMethods::post("name"),
                "phone" => RequestMethods::post("phone")
            );
            $student = $this->saveStudent($options);
        }

        if (RequestMethods::post("action") == "register") {
            $application = new Application(array(
                "student_id" => $student->id,
                "opportunity_id" => $opportunity->id,
                "property_id" => "",
                "status" => "register",
                "updated" => ""
            ));
            $application->save();

            $this->notify(array(
                "template" => "applicationTraining",
                "subject" => $opportunity->title,
                "opportunity" => $opportunity,
                "user" => User::first(array("id = ?" => $student->user_id), array("name", "email"))
            ));
            $view->set("success", TRUE);
            $view->set("application", $application);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function post() {
        $this->seo(array("title" => "Post Training", "keywords" => "training", "description" => "Your company trainings on linkedin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "training") {
            $opportunity = new Opportunity(array(
                "user_id" => $this->user->id, "organization_id" => $this->employer->organization->id,
                "title" => RequestMethods::post("title"),
                "details" => RequestMethods::post("details"),
                "eligibility" => RequestMethods::post("eligibility"),
                "category" => RequestMethods::post("category"),
                "duration" => RequestMethods::post("duration"),
                "location" => RequestMethods::post("location"),
                "type" => "training",
                "last_date" => RequestMethods::post("last_date"),
                "payment" => RequestMethods::post("payment"),
                "payment_mode" => "offline",
                "application_type" => "payment", "type_id" => "", "is_active" => "1", "validity" => "0", "updated" => ""
            ));

            if ($opportunity->validate()) {
                $opportunity->save();
                if (RequestMethods::post("linkedin") == "1") {
                    $this->shareupdate(array(
                        "content" => array(
                            "title" => $opportunity->title,
                            "description" => substr(strip_tags($opportunity->details), 0, 150),
                            "submitted-url" => "http://swiftintern.com/internship/" . urlencode($opportunity->title) . "/" . $opportunity->id
                        ), "visibility" => array("code" => "anyone")
                            ), $opportunity);
                }

                $instamojo = Registry::get("instamojo");
                $instamojo->linkCreate(array(
                    'title' => $opportunity->title,
                    'description' => $opportunity->details,
                    'base_price' => $opportunity->payment,
                    'currency' => 'INR',
                    'cover_image' => APP_PATH . '/public/assets/images/logo.png',
                    'webhook_url' => 'http://swiftintern.com/training/payment',
                    'redirect_url' => URL
                ));
                
                self::redirect('/training/manage');
            }

            $view->set("opportunity", $opportunity);
            $view->set("errors", $opportunity->getErrors());
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function edit($id) {
        $training = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id, "type = ?" => "training"));
        $this->seo(array("title" => "Edit Training", "keywords" => "edit", "description" => "edit", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "update") {
            $training->title = RequestMethods::post("title");
            $training->eligibility = RequestMethods::post("eligibility");
            $training->last_date = RequestMethods::post("last_date");
            $training->details = RequestMethods::post("details");
            $training->duration = RequestMethods::post("duration");
            $training->payment = RequestMethods::post("payment");
            $training->updated = date("Y-m-d H:i:s");

            $training->save();
            $view->set("success", true);
            $view->set("errors", $training->getErrors());
        }
        $view->set("training", $training);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function certificates($id) {
        $training = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id, "type = ?" => "training"));
        $this->seo(array("title" => "Edit Training", "keywords" => "edit", "description" => "edit", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $count = Participant::count(array("test_id = ?" => $training->type_id));
        $participants = Participant::all(array("test_id = ?" => $training->type_id), array("*"), $order, $direction, $limit, $page);

        $view->set("count", $count);
        $view->set("participants", $participants);
        $view->set("training", $training);
    }

    /**
     * @before _secure, changeLayout
     */
    public function manage() {
        $trainings = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id, "type = ?" => "training"), array("id", "title", "created"));
        $this->seo(array(
            "title" => "Manage Training",
            "keywords" => "training",
            "description" => "Your company trainings on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $view->set("trainings", $trainings);
    }
    
    public function organize() {
        $this->seo(array("title" => "Organize Workshops at your college", "keywords" => "workshop and training", "description" => "Organize Workshops and trainings at your campus", "view" => $this->getLayoutView()));
        
    }

    /**
     * @before _secure, changeLayout
     */
    public function applications($id) {
        $training = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id, "type = ?" => "training"), array("id", "title"));
        $this->seo(array("title" => "Training Applications", "keywords" => "Applications", "description" => "Applications received on internship posted", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $this->enroll($training);

        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 15);
        $count = Application::count(array("opportunity_id = ?" => $training->id));
        $applicants = Application::all(array("opportunity_id = ?" => $training->id), array("id", "student_id", "property_id", "status", "created"), $order, $direction, $limit, $page);

        $view->set("training", $training);
        $view->set("count", $count);
        $view->set("applicants", $applicants);
    }

    public function payment() {
        $this->noview();
        $instamojo = Registry::get("instamojo");

        try {
            $response = $instamojo->linksList();
            echo '<pre>', print_r($response), '</pre>';
        } catch (Exception $e) {
            print('Error: ' . $e->getMessage());
        }
    }

}
