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

        $orgs = Experience::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("orgs", $orgs);
    }

}
