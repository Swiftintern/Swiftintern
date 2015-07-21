<?php

/**
 * Controller for opportunities related request
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Opportunities extends Employer {

    public function type($title, $id) {
        $this->JSONview();
        $view = $this->getActionView();
        $opportunity = Opportunity::first(array("id = ?" => $id));

        self::redirect('/' . $opportunity->type . '/' . urlencode($title) . '/' . $id);
    }

    public function internship($title, $id) {
        global $datetime;
        $view = $this->getActionView();
        $session = Registry::get("session");
        $student = $session->get("student");

        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name", "photo_id"));
        if ($student) {
            $resume = Resume::first(array("student_id = ?" => $student->id), array("id"));
            $view->set("resume", $resume);
            $application = Application::first(array("student_id = ?" => $student->id, "opportunity_id = ?" => $id));
            $view->set("application", $application);
        }
        
        if (RequestMethods::post("quickApply") == "quickApply") {
            $options = array(
                "email" => RequestMethods::post("email", $this->user->email),
                "name" => RequestMethods::post("name"),
                "phone" => RequestMethods::post("phone", "")
            );
            $student = $this->saveStudent($options);
            if ($student) {
                $resume = new Resume(array(
                    "student_id" => $student->id,
                    "type" => "file",
                    "resume" => $this->_upload("file"),
                    "updated" => ""
                ));
                $resume->save();
                $view->set("success", true);
            }
        }

        if (RequestMethods::post("action") == "application") {
            $application = new Application(array(
                "student_id" => $student->id,
                "opportunity_id" => $opportunity->id,
                "property_id" => $resume->id,
                "status" => "applied",
                "updated" => ""
            ));
            $application->save();

            $this->notify(array(
                "template" => "applicationInternship",
                "subject" => "Internship Application",
                "opportunity" => $opportunity,
                "user" => $this->getUser()
            ));
            $view->set("success", TRUE);
            $view->set("application", $application);
        }

        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "photo" => APP . "thumbnails/" . $organization->photo_id,
            "view" => $this->getLayoutView()
        ));

        $view->set("enddate", $datetime->format("Y-m-d"));
        $view->set("opportunity", $opportunity);
        $view->set("organization", $organization);
    }

    protected function saveStudent($options) {
        $user = $this->read(array(
            "model" => "user",
            "where" => array("email = ?" => $options["email"])
        ));
        if ($user) {
            $student = Student::first(array("user_id = ?" => $user->id));
        } else {
            $user = new User(array(
                "name" => $options["name"],
                "email" => $options["email"],
                "phone" => $this->checkData($options["phone"]),
                "password" => rand(100000, 99999999),
                "access_token" => rand(100000, 99999999),
                "type" => "student",
                "validity" => "1",
                "last_ip" => $_SERVER['REMOTE_ADDR'],
                "last_login" => "1",
                "updated" => ""
            ));
            $user->save();
            $this->notify(array(
                "template" => "studentRegister",
                "subject" => "Getting Started on Swiftintern.com",
                "user" => $user
            ));
            $student = new Student(array(
                "user_id" => $user->id,
                "about" => $this->checkData($options["summary"]),
                "city" => $this->checkData($options["city"]),
                "skills" => $this->checkData($options["skills"]),
                "updated" => ""
            ));
            $student->save();
        }

        $this->user = $user;
        $session = Registry::get("session");
        $session->set("student", $student);
        return $student;
    }

    public function competition($title, $id) {
        global $datetime;
        $view = $this->getActionView();

        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name", "photo_id"));

        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "photo" => APP . "thumbnails/" . $organization->photo_id,
            "view" => $this->getLayoutView()
        ));

        $view->set("enddate", $datetime->format("Y-m-d"));
        $view->set("opportunity", $opportunity);
        $view->set("organization", $organization);
    }

    public function sponsored() {
        $this->JSONview();
        global $datetime;
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
            $sponsoreds = Opportunity::all(array("id = ?" => $sd->opportunity_id), array("id", "title", "location", "last_date", "eligibility"));
        }
        $this->getActionView()->set("sponsoreds", $sponsoreds);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function editinternship($id = NULL) {
        if ($id == NULL) {
            self::redirect("/employer/internships");
        }
        $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id));
        $this->seo(array("title" => "Edit Internship", "keywords" => "edit", "description" => "edit", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "update") {
            $internship->title = RequestMethods::post("title");
            $internship->eligibility = RequestMethods::post("eligibility");
            $internship->last_date = RequestMethods::post("last_date");
            $internship->details = RequestMethods::post("details");
            $internship->payment = RequestMethods::post("payment");
            $internship->updated = date("Y-m-d H:i:s");

            $internship->save();
            $view->set("success", true);
            $view->set("errors", $internship->getErrors());
        }
        $view->set("internship", $internship);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function postinternship() {
        $this->seo(array("title" => "Post Internship", "keywords" => "internshhip", "description" => "Your company internships on linkedin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "internship") {
            $opportunity = new Opportunity(array(
                "user_id" => $this->user->id, "organization_id" => $this->employer->organization->id,
                "title" => RequestMethods::post("title"),
                "details" => RequestMethods::post("details"),
                "eligibility" => RequestMethods::post("eligibility"),
                "category" => RequestMethods::post("category"),
                "duration" => RequestMethods::post("duration"),
                "location" => RequestMethods::post("location"),
                "type" => "internship",
                "last_date" => RequestMethods::post("last_date"),
                "payment" => RequestMethods::post("payment"),
                "payment_mode" => "offline", "application_type" => "resume", "type_id" => "", "is_active" => "1", "validity" => "0", "updated" => ""
            ));

            if ($opportunity->validate()) {
                $opportunity->save();
                if (RequestMethods::post("linkedin") == "1") {
                    $this->shareupdate(array(
                        "content" => array(
                            "title" => $opportunity->title,
                            "description" => substr(strip_tags($opportunity->details), 0, 150),
                            "submitted-url" => "http://swiftintern.com/internship/" . urlencode($opportunity->title) . "/" . $opportunity->id
                        ), "visibility" => array("code" => "anyone")
                            ), $opportunity);
                }
                self::redirect('/employer/internships');
            }

            $view->set("opportunity", $opportunity);
            $view->set("errors", $opportunity->getErrors());
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function internships() {
        $internships = Opportunity::all(array("organization_id = ?" => $this->employer->organization->id, "type = ?" => "internship"), array("id", "title", "created"));
        $this->seo(array(
            "title" => "Manage Internships",
            "keywords" => "followers",
            "description" => "Your company followers on linkedin",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $view->set("internships", $internships);
    }

    /**
     * @before _secure, changeLayout
     */
    public function applicants($id = NULL) {
        if ($id == NULL) {
            self::redirect("/employer/internships");
        }
        $internship = Opportunity::first(array("id = ? " => $id, "organization_id = ? " => $this->employer->organization->id), array("id", "title"));
        $this->seo(array("title" => "Applications","keywords" => "Applications","description" => "Applications received on internship posted","view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $shortlisted = [];$selected = [];$applied = [];$applicants = [];$rejected = [];

        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 15);
        $count = Application::count(array("opportunity_id = ?" => $internship->id));
        $applications = Application::all(array("opportunity_id = ?" => $internship->id), array("id", "student_id", "property_id", "status", "created"), $order, $direction, $limit, $page);

        foreach ($applications as $application) {
            $student = Student::first(array("id = ?" => $application->student_id), array("user_id", "about"));
            $user = User::first(array("id = ?" => $student->user_id), array("name"));

            $applicant = \Framework\ArrayMethods::toObject(array(
                        "id" => $application->id,
                        "name" => $user->name,
                        "student_id" => $application->student_id,
                        "property_id" => $application->property_id,
                        "status" => $application->status,
                        "created" => $application->created
            ));
            $applicants[] = $applicant;
            switch ($application->status) {
                case "shortlisted":
                    $shortlisted[] = $applicant;
                    break;
                case "selected":
                    $selected[] = $applicant;
                    break;
                case "applied":
                    $applied[] = $applicant;
                    break;
                case "rejected":
                    $rejected[] = $applicant;
                    break;
            }
        }

        $view->set("internship", $internship);
        $view->set("count", $count);
        $view->set("shortlisted", Framework\ArrayMethods::toObject($shortlisted));
        $view->set("selected", Framework\ArrayMethods::toObject($selected));
        $view->set("applied", Framework\ArrayMethods::toObject($applied));
        $view->set("rejected", Framework\ArrayMethods::toObject($rejected));
        $view->set("applicants", Framework\ArrayMethods::toObject($applicants));
    }

}
