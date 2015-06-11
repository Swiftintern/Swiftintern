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
        
        // Get Details for the student - education, skills
        $student = Registry::get('session')->get("student");
        $qual = Qualification::all(array(
            "student_id = ?" => $student->id
        ));
        $work = Work::all(array(
            "student_id = ?" => $student->id
        ));
        $skills = $student->skills;

        // If details not found redirect the user to resume builder for saving the details
        if (empty($qual) || empty($work) || empty($skills)) {
            self::redirect("/resumes/create");
        }

        $view->set('info', $info);
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
        
        $student = Registry::get('session')->get('student');

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
                case 'saveQual':
                    $set = $this->saveQual();
                    $qual = $set['qualification'];
                    break;
                
                case 'saveWork':
                    $set = $this->saveWork();
                    $work = $set['work'];

                    if (RequestMethods::post("skills", "")) {
                       $student->skills = $skills = RequestMethods::post("skills");
                       $student->updated = date('Y-m-d H:i:s');
                       $student->save();
                    }
                    break;

                case 'saveSkills':
                    $skills = RequestMethods::post("skills");
                    if (!empty($skills)) {
                        $student->skills = $skills;
                        $student->updated = date('Y-m-d H:i:s');
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
        $view->set("qual", (empty($qual) ? "required" : false));
    }
    
    public function view($resume_id) {
        $this->noview();
        $resume = Resume::first(array("id = ?" => $resume_id));
        if($resume){
            switch ($resume->type) {
                case "file":
                    header("Location: https://docs.google.com/gview?url=http://assets.swiftintern.com/uploads/files/{$resume->resume}");
                    break;
                case "text":
                    echo $resume->resume;
                    break;
            }
        } else {
            echo 'Resume does not exist';
        }
    }
}
