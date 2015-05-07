<?php

/**
 * Description of Organizations
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Organizations extends Controller {

    public function index() {
        $this->seo(array(
            "title"         => "Companies | Organizations | NGO | Colleges",
            "keywords"      => "company, organization, ngo, internship",
            "description"   => "Comapnies which have used swiftintern to hire interns.",
            "view"          => $this->getLayoutView()
        ));
        
        $name = RequestMethods::get("name", "");
        $type = RequestMethods::get("type", "");
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        
        $organizations = Organization::all(
            array(
                "name LIKE ?" => "%{$name}%",
                "type LIKE ?" => "%{$type}%",
            ),
            array("id", "name"),
            $order, $direction, $limit, $page
        );
        
        $this->getActionView()->set("organizations", $organizations);
    }
    
    public function photo($organization_id) {
        $org = Organization::first(array("id = ?" => $organization_id),array("photo_id"));
        self::redirect("/thumbnails/{$org->photo_id}");
    }
    
    public function organization($name, $id) {
        $view = $this->getActionView();
        $organization = Organization::first(
            array("id = ?" => $id),
            array("id", "name", "address", "phone", "website", "type", "linkedin_id", "photo_id")
        );
        
        $opportunities = Opportunity::all(
            array("organization_id = ?" => $organization->id),
            array("id", "title", "last_date", "location")
        );
        
        $experiences = Experience::all(
            array("organization_id = ?" => $organization->id),
            array("id", "title", "details")
        );
        
        $this->seo(array(
            "title"         => "Companies | Organizations | NGO | Colleges",
            "keywords"      => "company, organization, ngo, internship",
            "description"   => "Comapnies which have used swiftintern to hire interns.",
            "view"          => $this->getLayoutView()
        ));
        
        $view->set("organization", $organization);
        $view->set("opportunities", $opportunities);
        $view->set("experiences", $experiences);
    }

}
