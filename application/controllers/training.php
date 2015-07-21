<?php

/**
 * Description of training
 *
 * @author Faizan Ayubi
 */

use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Training extends Employer {
    
    public function details($title, $id) {
        global $datetime;
        $view = $this->getActionView();

        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name", "photo_id"));

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
                "application_type" => "", "type_id" => "", "is_active" => "1", "validity" => "0", "updated" => ""
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
                self::redirect('/opportunities/internships');
            }

            $view->set("opportunity", $opportunity);
            $view->set("errors", $opportunity->getErrors());
        }
    }
    
    public function edit($id) {
        
    }
    
    public function manage() {
        
    }
    
    public function applicants($id) {
        
    }
    
}
