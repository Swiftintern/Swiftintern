<?php

/**
 * Description of placementpaper
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class PlacementPaper extends Controller{

    public function companies() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Companies Placement Papers, Experiences");
        $seo->setKeywords("placement papers");
        $seo->setDescription("Browse through thousands of placement papers and experiences shared by thousands of student across india");
        
        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        $query = RequestMethods::post("query", "");
        $order = RequestMethods::post("order", "created");
        $direction = RequestMethods::post("direction", "desc");
        $page = RequestMethods::post("page", 1);
        $limit = RequestMethods::post("limit", 10);
        
        $where = array(
            "validity = ?" => true
        );

        $fields = array(
            "DISTINCT organization_id"
        );

        $companies = Experience::all($where, $fields, $order, $direction, $limit, $page);
        $orgs = array();
        
        foreach ($companies as $company){
            $organization = Organization::first(
                array("id = ?" => $company->organization_id),
                array("id", "name", "photo_id")
            );
            if ($organization->photo_id) {
                    $photo_id = $organization->photo_id;
            }else {
                    $photo_id = LOGO;
            }
            $orgs[] = array(
                'id'        => $organization->id,
                'name'      => $organization->name,
                'photo_id'  => $photo_id
            );
        }

        $view->set("orgs", $orgs);
    }
    
    public function experience($id) {
        $seo = Framework\Registry::get("seo");
        
        var_dump($id);

        $seo->setTitle("Companies Placement Papers, Experiences");
        $seo->setKeywords("placement papers");
        $seo->setDescription("Browse through thousands of placement papers and experiences shared by thousands of student across india");
        
        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        
    }

}
