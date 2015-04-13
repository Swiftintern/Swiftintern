<?php

/**
 * Class containing all action of resumes
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Resumes extends Controller {

    public function about() {
        $seo = Registry::get("seo");

        $seo->setTitle("Resume for Internship, Job | Create and Edit online");
        $seo->setKeywords("resume for internship, resume, resume online, cv for internship");
        $seo->setDescription("Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();

        $total_resumes = Resume::count();
        $total_users = User::count();
        $today_resume = 0;

        $view->set("total_resumes", $total_resumes);
        $view->set("total_users", $total_users);
        $view->set("today_resume", $today_resume);
    }

    public function create() {
        include APP_PATH . '/public/datalist.php';
        $seo = Registry::get("seo");

        $seo->setTitle("Resume for Internship, Job | Create and Edit online");
        $seo->setKeywords("resume for internship, resume, resume online, cv for internship");
        $seo->setDescription("Swiftintern.com is a great place to build and post your resume online for free. Its easy to sign up, free to use, and you can access your resume from anywhere once you have posted it. Use our free resume builder to create the perfect resume online in minutes.");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();

        $colleges = Organization::all(
                        array(
                    "type = ?" => "institute"
                        ), array("name")
        );

        $companys = Organization::all(
                        array(
                    "type = ?" => "company"
                        ), array("name")
        );

        $view->set("colleges", $colleges);
        $view->set("companys", $companys);
        $view->set("allmajors", $allmajors);
        $view->set("alldegrees", $alldegrees);
    }

    /**
     * @before _secure
     */
    public function success() {
        $seo = Registry::get("seo");
        $session = Registry::get("session");
        $user = $this->user;
        $student = $session->get("student");

        $seo->setTitle($user->name." Resume");
        $seo->setKeywords("Resume");
        $seo->setDescription("Edit, Delete and create a new resume");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();
        
        $qualifications = Qualification::all(
            array("student_id = ?" => $student->id),
            array("id", "degree", "major", "organization_id", "gpa", "passing_year")
        );
        $works = Work::all(
            array("student_id = ?" => $student->id),
            array("id", "designation", "responsibility", "organization_id", "duration")
        );
        
        $view->set("student", $student);
        $view->set("qualifications", $qualifications);
        $view->set("works", $works);
    }

}
