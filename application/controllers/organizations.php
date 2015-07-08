<?php

/**
 * Controller to Manage all Organization related stuffs such as profile, experience shared, photos, opportunities
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class Organizations extends Users {

    public function index() {
        $this->seo(array(
            "title"         => "Companies | Organizations | NGO | Colleges",
            "keywords"      => "company, organization, ngo, internship",
            "description"   => "Comapnies which have used swiftintern to hire interns.",
            "view"          => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $name = RequestMethods::get("name", "");
        $type = RequestMethods::get("type", "");
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        
        $where = array(
            "name LIKE ?" => "%{$name}%",
            "type LIKE ?" => "%{$type}%",
        );
        
        $count = Organization::count($where);
        $organizations = Organization::all(
            $where,
            array("id", "name", "photo_id"),
            $order, $direction, $limit, $page
        );
        
        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("count", $count);
        $view->set("organizations", $organizations);
    }
    
    public function photo($organization_id) {
        $org = Organization::first(array("id = ?" => $organization_id),array("photo_id"));
        self::redirect("/thumbnails/{$org->photo_id}");
    }
    
    public function organization($name, $id) {
        $this->seo(array(
            "title"         => "{$name} Placement Papers, internship",
            "keywords"      => "company, organization, ngo, internship",
            "description"   => "Comapnies which have used swiftintern to hire interns.",
            "view"          => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $organization = Organization::first(array("id = ?" => $id),array("id", "name", "website", "type", "linkedin_id", "photo_id"));
        $opportunities = Opportunity::all(array("organization_id = ?" => $organization->id),array("id", "title", "last_date", "location"));
        $experiences = Experience::all(array("organization_id = ?" => $organization->id),array("id", "title", "details"));
        
        $view->set("organization", $organization);
        $view->set("opportunities", $opportunities);
        $view->set("experiences", $experiences);
    }
    
    public function placementpapers() {
        $this->seo(array(
            "title"         => "Companies Placement Papers, Experiences",
            "keywords"      => "placement papers",
            "description"   => "Browse through thousands of placement papers and experiences shared by thousands of student across india",
            "view"          => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 12);
        
        $where = array("validity = ?" => true);
        $fields = array("DISTINCT organization_id");
        $count = Experience::count($where);
        $companies = Experience::all($where, $fields, $order, $direction, $limit, $page);
        
        $organizations = array();
        foreach ($companies as $company){
            $organization = Organization::first(
                array("id = ?" => $company->organization_id),
                array("id", "name", "photo_id")
            );
            $organizations[] = $organization;
        }

        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("count", $count);
        $view->set("organizations", $organizations);
    }
    
    public function experience($title, $id) {
        $seo = Framework\Registry::get("seo");
        
        $experience = Experience::first(
            array("id = ?" => $id),
            array("id", "organization_id", "title", "details")
        );
        $organization   = Organization::first(
            array("id = ?" => $experience->organization_id),
            array("id", "name")
        );
        
        $seo->setTitle($title." by ".$organization->name);
        $seo->setKeywords($title.", {$organization->name} PLacement Paper");
        $seo->setDescription(substr(strip_tags($experience->details), 0, 155));
        
        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        $next = 0;
        $previous = 0;
        $experiences = Experience::all(
            array("organization_id = ?" => $experience->organization_id,"validity = ?" => true),
            array("id", "organization_id", "title", "details")
        );

        foreach($experiences as $exp) {
                if($exp->id > $experience->id) {
                        $next = $exp->id;
                        break;
                }
                if($exp->id < $experience->id) {
                        $previous = $exp->id;
                }
        }
        
        $view->set("next", $next);
        $view->set("previous", $previous);
        $view->set("experience", $experience);
        $view->set("organization", $organization);
    }
    
    public function saveExperience($organization_id = NULL) {
        $this->seo(array(
            "title"         => "Your experience",
            "keywords"      => "add placement papers, experience",
            "description"   => "experience",
            "view"          => $this->getLayoutView()
        ));$view = $this->getActionView();
        $view->set("success", false);
        
        if(RequestMethods::post("title")){
            $userid = !empty($this->user->id) ? $this->user->id : "0";
            $experience = new Experience(array(
                "organization_id" => RequestMethods::post("organization_id", $organization_id),
                "user_id" => RequestMethods::post("user_id", $userid),
                "title" => RequestMethods::post("title"),
                "details" => RequestMethods::post("details"),
                "validity" => "0"
            ));
            $experience->save();
            $view->set("success", true);
        }
        
        $view->set("organization_id", $organization_id);
    }
}