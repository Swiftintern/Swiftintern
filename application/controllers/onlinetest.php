<?php

/**
 * Description of test
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class OnlineTest extends Users {

    public function index() {
        $this->seo(array(
            "title" => "Online Test with Certificate",
            "keywords" => "online test, practice test, online exams, skills verification",
            "description" => "Appear to Online Exam and verify your skills for getting internship.",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $query = RequestMethods::get("query", "");
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 12);

        $where = array("is_active = ?" => true, "validity = ?" => true);

        $fields = array("id", "title");

        $count = Test::count($where);
        $exams = Test::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("limit", $limit);
        $view->set("count", $count);
        $view->set("exams", $exams);
    }

    public function photo($test_id) {
        $image = Image::first(array("property = ? " => "test", "property_id = ?" => $test_id), array("photo_id"));
        self::redirect("/thumbnails/{$image->photo_id}");
    }

    public function details($title, $id) {
        $test = Test::first(array("id = ?" => $id));
        $this->seo(array(
            "title" => $test->title,
            "keywords" => $title,
            "description" => strip_tags($test->syllabus),
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $view->set("test", $test);
    }
    
    /**
     * @before _secure
     */
    public function test($title, $id) {
        $test = Test::first(array("id = ?" => $id), array("id", "title", "syllabus", "type", "subject", "time_limit"));
        $this->seo(array(
            "title" => $test->title,
            "keywords" => $title,
            "description" => strip_tags($test->syllabus),
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        $questions = Question::all(array("test_id = ?" => $test->id), array("id", "question", "type"));

        $participant = Participant::first(array("test_id = ?" => $test->id, "user_id = ?" => $this->user->id));
        if(!$participant){
            $participant = new Participant(array(
                "test_id" => $test->id,
                "user_id" => $this->user->id,
                "score" => "", "time_taken" => "", "attempted" => ""
            ));$participant->save();
        }

        $view->set("participant", $participant);
        $view->set("test", $test);
        $view->set("questions", $questions);
    }

    public function result($participant_id) {
        $participant = Participant::first(array("id = ?" => $participant_id));
        $test = Test::first(array("id = ?" => $participant->test_id));
        $this->seo(array(
            "title" => $test->title,
            "keywords" => $test->title,
            "description" => strip_tags($test->syllabus),
            "view" => $this->getLayoutView()
        ));$view = $this->getActionView();

        if (RequestMethods::post("action") == "test_result") {
            $total_questions = Question::count(array("test_id = ?" => $test->id));
            $per_ques = 100 /$total_questions;$marks = 0;$count = 0;
            $questions = RequestMethods::post("question");
            
            foreach ($questions as $question => $answer) {
                $option = Option::first(array("id = ?" => $answer), array("is_answer"));
                if ($option->is_answer) {
                    $marks += $per_ques;
                }$count++;
            }
            $participant->score = $marks;
            $participant->attempted = $count;
            
            $participant->save();
        }
        $view->set("participant", $participant);
        $view->set("test", $test);
    }

    public function test_certi($certi_id) {
        if (empty($certi_id)) {
            self::redirect("/tests");
        }
        $certificate = Certificate::first(array(
                    "uniqid = ?" => $certi_id
        ));

        if (!$certificate) {
            self::redirect("/tests");
        }
        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();

        $participant = Participant::first(array('id = ?' => $certificate->property_id));
        $test = Test::first(array('id = ?' => $participant->test_id));
        $user = User::first(array('id = ?' => $participant->user_id));
        $student = Student::first(array('user_id = ?' => $participant->user_id));

        $seo->setTitle($user->name . "'s certificate of " . $test->title);
        $seo->setKeywords($user->name . "'s certificate of " . $test->title);
        $seo->setDescription($user->name . "'s certificate of " . $test->title);
        $this->getLayoutView()->set("seo", $seo);

        $view->set("participant", $participant);
        $view->set("test", $test);
        $view->set("user", $user);
        $view->set("student", $student);
        $view->set("certificate", $certificate);
    }

    public function test_participated($title, $id) {
        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();
        $test = Test::first(array(
                    "id = ?" => $id
        ));
        $image = Image::first(array(
                    "property = ?" => "test",
                    "property_id = ?" => $test->id
        ));
        if ($image) {
            $photo = Photograph::first(array("id = ?" => $image->photo_id));
        } else {
            $organization = Organization::first(array("id = ?" => $test->organization_id));
            $photo = Photograph::first(array("id = ?" => $organization->photo_id));
        }
        $seo->setTitle($test->title);
        $seo->setKeywords($test->title);
        $seo->setDescription(strip_tags($test->syllabus));
        $this->getLayoutView()->set("seo", $seo);
        $view->set("test", $test);
        $view->set("photo", $photo);
    }

    public function certification() {
        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();

        $seo->setTitle("Online Certification Test");
        $seo->setKeywords("online certification, get certificate, ");
        $seo->setDescription("Get certified Online by Appearing in the test of various topics");
        $this->getLayoutView()->set("seo", $seo);

        $tests = Test::all(array(
                    "validity = ?" => true,
                    "type = ?" => "certification",
                    "is_active = ?" => true
        ));

        $view->set("tests", $tests);
    }

}