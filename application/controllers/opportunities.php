<?php

/**
 * Description of Opportunity
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Opportunities extends Controller {

    public function view() {
        echo 'here';
    }

    public function sponsored() {
        global $datetime;
        $this->seo(array(
            "title"         => "Get Internship | Student Register",
            "keywords"      => "get internship, student register",
            "description"   => "Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc",
            "view"          => $this->getLayoutView()
        ));
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
            $sponsoreds = Opportunity::all(
                array(
                    "id = ?" => $sd->opportunity_id
                ),
                array("id", "title", "location", "last_date", "eligibility")
            );
        }
        $this->actionView->set("sponsoreds", $sponsoreds);
    }
    
    public function search() {
        $query = RequestMethods::get("query", "");
        $order = RequestMethods::get("order", "id");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        
        $where = array(
            "title LIKE ?" => "%{$query}%",
            "category LIKE ?" => "%{$query}%",
            "validity = ?" => true
        );

        $fields = array(
            "id", "title", "eligibility", "location", "last_date"
        );

        $opportunities = Opportunity::all($where, $fields, $order, $direction, $limit, $page);
        
        $this->getActionView()->set("query", $query);
        $this->getActionView()->set("opportunities", $opportunities);
    }

}
