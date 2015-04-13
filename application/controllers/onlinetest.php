<?php

/**
 * Description of test
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class OnlineTest extends Controller {

    public function index() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Online Test with Certificate");
	$seo->setKeywords("online test, practice test, online exams, skills verification");
	$seo->setDescription("Appear to Online Exam and verify your skills for getting internship.");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        $query = RequestMethods::post("query", "");
        $order = RequestMethods::post("order", "created");
        $direction = RequestMethods::post("direction", "desc");
        $page = RequestMethods::post("page", 1);
        $limit = RequestMethods::post("limit", 10);
        
        $where = array(
            "is_active = ?" => true,
            "validity = ?" => true
        );

        $fields = array(
            "id", "title"
        );

        $count = Test::count($where);
        $exams = Test::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("exams", $exams);
    }

}
