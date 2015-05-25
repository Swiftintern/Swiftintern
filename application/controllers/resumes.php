<?php

/**
 * Class containing all action of resumes
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Resumes extends Students {

    public function index() {
        $this->seo(array(
            "title" => "Resume for Internship | Create and Edit online",
            "keywords" => "resume for internship, resume, resume online, cv for internship",
            "description" => "Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $li = $this->LinkedIn("http://swiftintern.com/resumes");
        if (isset($_REQUEST['code'])) {
            $li->getAccessToken($_REQUEST['code']);
            //self::redirect('/resumes/success');
        } else {
            $url = $li->getLoginUrl(array(LinkedIn::SCOPE_BASIC_PROFILE, LinkedIn::SCOPE_EMAIL_ADDRESS));
            $view->set("url", $url);
        }
        $this->getActionView()->set("li", $li);
    }
    
    public function guidelines() {
        $this->seo(array(
            "title" => "Internship Resume Guideline",
            "keywords" => "guidelines to create internship resume",
            "description" => "Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.",
            "view" => $this->getLayoutView()
        ));
    }

    /**
     * @before _secure
     */
    public function success() {
        $this->seo(array( 
            "title" => "Resume",
            "keywords" => "resume",
            "description" => "Edit resume online",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $li = Registry::get('linkedin');
        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(phone-numbers,summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills)');
        }
        $view->set('info', $info);
        //echo '<pre>', print_r($info), '</pre>';
    }

}
