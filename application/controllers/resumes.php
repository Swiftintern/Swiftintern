<?php

/**
 * Class containing all action of resumes creating, publisinhg, sharing
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;
use Framework\ArrayMethods as ArrayMethods;
use PHPExport\Exporter\Xml as Xml;
use PHPExport\Exporter\MsWord as MsWord;

class Resumes extends Students {

    public function index() {
        $this->seo(array(
            "title" => "Resume for Internship | Create and Edit online",
            "keywords" => "resume for internship, resume, resume online, cv for internship",
            "description" => "Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.",
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();
        
        $li = $this->LinkedIn("http://swiftintern.com/students/register");
        if (isset($_REQUEST['code'])) {
            $li->getAccessToken($_REQUEST['code']);
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

        /* Create an array containing all details of resume */
        $resume = array(
            "name" => $this->user->name,
            "email" => $this->user->email,
            "phone" => (($this->user->phone == NULL) ? "(Phone)" : $this->user->phone),
            "city" => $student->city,
            "about" => (($student->about == NULL) ? "(Your Objective)" : $student->about),
            "education" => $this->getEducation($qual),
            "work" => $this->getWork($work),
            "skills" => $student->skills
        );

        /* Convert the resume to an object of 'stdClass' */
        $resume = ArrayMethods::toObject($resume);
        /* Create the xml from the object */
        $resume_xml = new Xml(array($resume), null);
        
        /* Store the resume XML template in session so that it can be accessed in download() method */
        $session = Registry::get('session');
        $session->set('resume', $resume_xml);

        $view->set('user', $this->user);
        $view->set('student', $student);
        $view->set('edu', $qual);
        $view->set('works', $work);
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

    protected function getEducation($quals) {
        $arr = array();
        foreach ($quals as $edu) {
            $org = Organization::first(array("id = ?" => $edu->organization_id), array("name"));
            $arr[] = array(
                "institute" => $org->name,
                "major" => $edu->major,
                "degree" => $edu->degree,
                "year" => $edu->passing_year,
                "gpa" => $edu->gpa
            );
        }
        return $arr;
    }

    protected function getWork($works) {
        $arr = array();
        foreach ($works as $work) {
            $org = Organization::first(array("id = ?" => $work->organization_id), array("name"));
            $arr[] = array(
                "position" => $work->designation,
                "company" => $org->name,
                "duration" => $work->duration,
                "responsibility" => $work->responsibility
            );
        }
        return $arr;
    }

    /**
     * @before _secure, changeLayout
     */
    public function download() {
        $session = Registry::get('session');
        $resume = $session->get('resume');

        if(!$resume) {
            self::redirect('/resumes/success');
        }

        $download = new MsWord($resume);
        $dir = APP_PATH.'/public/assets/files/resume/';
        
        /* The template created in word processor */
        $download->setDocTemplate($dir . 'resume.docx');
        
        /* The XSLT stylesheet extracted from word */
        $download->setXsltSource($dir . 'resume.xslt');
        
        /* Merge XML and XSLT stylesheet to generate a new docx file (ZIP Archive) */
        $download->create('resume22.docx');
    }
}
