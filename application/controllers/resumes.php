<?php

/**
 * Class containing all action of resumes creating, publisinhg, sharing
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
     * @before _secure, changeLayout
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
        $student = Registry::get('session')->get("student");
        $qual = Qualification::all(array(
            "student_id = ?" => $student->id
        ));
        $skills = $student->skills;
        if (empty($qual)) {
            self::redirect("/resumes/create");
        }

        $view->set('info', []);
        $view->set('edu', $qual);
        $view->set('skills', $skills);
        //echo '<pre>', print_r($info), '</pre>';
    }
    
    public function test() {
        $this->noview();
        $li = Registry::get('linkedin');
        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(phone-numbers,summary,first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills)');
        }
        echo '<pre>', print_r($info), '</pre>';
    }

    /**
     * @before _secure, changeLayout
     */
    public function create() {
        $this->seo(array(
            "title" => "Resume for Internship | Create and Edit online",
            "keywords" => "resume for internship, resume, resume online, cv for internship",
            "description" => "Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();
        
        $session = Registry::get("session");
        $student = $session->get("student");
        $user = $this->user;

        $qual = Qualification::all(array(
            "student_id = ?" => $student->id
        ));
        $work = Work::all(array(
            "student_id = ?" => $student->id
        ));
        $skills = $student->skills;
        
        // Check which form has been submitted
        $action = RequestMethods::post("action");
        if ($action) {
            switch ($action) {
                case 'update_qualification':
                    $institute = RequestMethods::post("name");
                    $degree = RequestMethods::post("degree");
                    $major = RequestMethods::post("major");
                    $gpa = RequestMethods::post("gpa");
                    $passYr = RequestMethods::post("passing_year");

                    if (!empty($institute)) {
                        $i = 0;
                        foreach ($institute as $inst) {
                           $org = Organization::first(array(
                                "name = ?" => $inst,
                                "type = ?" => "institute"
                            ));
                           if(!$org) {
                               $org = new Organization(array(
                                    "name" => $inst,
                                    "type" => "institute"
                                ));
                               $org->save();
                           }

                           $qual = new Qualification(array(
                               "student_id" => $student->id,
                               "organization_id" => $org->id,
                               "degree" => $degree[$i],
                               "major" => $major[$i],
                               "gpa" => $gpa[$i],
                               "passing_year" => $passYr[$i]
                           ));
                           $qual->save();
                           $i++;
                        }   
                    }
                    break;
                
                case 'update_work':
                    $company = RequestMethods::post("name");
                    $duration = RequestMethods::post("duration");
                    $responsibility = RequestMethods::post("responsibility");
                    $designation = RequestMethods::post("designation");

                    if (!empty($company)) {
                        $i = 0;
                        foreach ($company as $comp) {
                           $org = Organization::first(array(
                                "name = ?" => $comp,
                                "type = ?" => "company"
                            ));
                           if(!$org) {
                               $org = new Organization(array(
                                    "name" => $comp,
                                    "type" => "company"
                                ));
                               $org->save();
                           }

                           $work = new Work(array(
                               "student_id" => $student->id,
                               "organization_id" => $org->id,
                               "duration" => $duration[$i],
                               "designation" => $designation[$i],
                               "responsibility" => $responsibility[$i]
                           ));
                           $work->save();

                           if (!empty(RequestMethods::post("skills"))) {
                               $student->skills = RequestMethods::post("skills");
                               $student->save();
                           }
                        }
                    }
                    break;

                case 'update_skills':
                    $skills = RequestMethods::post("skills");
                    if (!empty($skills)) {
                        $student->skills = $skills;
                        $student->save();
                    }
                    break;
            }
        }

        // If everything - Work, Qualification, and Skills saved in db then redirect
        // Because additional info can be added through student panel
        if(!empty($qual) && !empty($work) && !empty($skills)) {
             self::redirect("/resumes/success");
        }

        // If skill/work/education not saved in db then required
        $view->set("skills", (empty($skills) ? "required" : false));
        $view->set("work", (empty($work) ? "required" : false));
        $view->set("edu", (empty($qual) ? "required" : false));
    }
}
