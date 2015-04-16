<?php

/**
 * Description of home
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Home extends Controller {

    public function index() {
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
            "id", "title", "eligibility", "location", "last_date"
        );

        $count = Opportunity::count($where);
        $opportunities = Opportunity::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("limit", $limit);
        $view->set("opportunities", $opportunities);
        
        $this->getLayoutView()->set("seo", Framework\Registry::get("seo"));
    }

    public function about() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("About Us");
        $seo->setKeywords("about us, how swiftintern works, swiftintern");
        $seo->setDescription("SwiftIntern is the india's largest student-focused internship portal, bringing students, employers and higher education institutions together in one centralized location.");
        $seo->setPhoto("http://assets.swiftintern.com/img/newsletter/header.png");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function contact() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Contact Us");
        $seo->setKeywords("contact, report problem, swiftintern");
        $seo->setDescription("We would love to hear from you. contact us to know more.");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function privacy() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Privacy Policy");
        $seo->setKeywords("privacy policy");
        $seo->setDescription("We collect information from you when you register on our site, place an order, subscribe to our newsletter, respond to a survey or fill out a form. ");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function blog() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Intern Blog");
        $seo->setKeywords("blog, Intern Blog, internship tips, internship advice, internship discussions");
        $seo->setDescription("Internship blogs post tips, advice to students to achieve the most from their internship and how to avail maximum benefits during an intern period.");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function post() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle($post->title);
        $seo->setKeywords($post->category);
        $seo->setDescription(substr(strip_tags($post->content), 0, 150));
        $seo->setPhoto($photo->image_path_thumb());

        $this->getLayoutView()->set("seo", $seo);
    }

    public function termsofservice() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Terms of Service");
        $seo->setKeywords("terms of use, refund policy, swiftintern");
        $seo->setDescription("Following is the agrrement of use on swiftintern including refund policy.");

        $this->getLayoutView()->set("seo", $seo);
    }
    
    public function hemant() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Terms of Service");
        $seo->setKeywords("terms of use, refund policy, swiftintern");
        $seo->setDescription("Following is the agrrement of use on swiftintern including refund policy.");

        $this->getLayoutView()->set("seo", $seo);
        
        $this->getLayoutView()->set("name", 'hemant');
    }
}