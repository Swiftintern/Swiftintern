<?php

/**
 * Controller for opportunities related request
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Opportunities extends Users{
    
    public function index($title, $id) {
        $this->JSONview();
        $view = $this->getActionView();
        $opportunity = Opportunity::first(array("id = ?" => $id));

        self::redirect('/' . $opportunity->type . '/' . urlencode($title) . '/' . $id);

        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "view" => $this->getLayoutView()
        ));

        $view->set("opportunity", $opportunity);
    }
    
    public function internship($title, $id) {
        global $datetime;
        $view = $this->getActionView();
        $session = Registry::get("session");
        $student = $session->get("student");
        
        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name", "photo_id"));
        if ($student) {
            $resume = Resume::first(array("student_id = ?" => $student->id), array("id"));
            $view->set("resume", $resume);
            $application = Application::first(array("student_id = ?" => $student->id, "opportunity_id = ?" => $id));
            $view->set("application", $application);
        }

        if (RequestMethods::post("action") == "application") {
            $application = new Application(array(
                "student_id" => $student->id,
                "opportunity_id" => $opportunity->id,
                "property_id" => $resume->id,
                "status" => "applied",
                "updated" => ""
            ));
            $application->save();

            $this->notify(array(
                "template" => "applicationInternship",
                "subject" => "Internship Application",
                "opportunity" => $opportunity,
                "user" => $this->getUser()
            ));
            $view->set("success", TRUE);
            $view->set("application", $application);
        }

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
            $sponsoreds = Opportunity::all(array("id = ?" => $sd->opportunity_id), array("id", "title", "location", "last_date", "eligibility"));
        }
        $this->getActionView()->set("sponsoreds", $sponsoreds);
    }
    
    
    
}
