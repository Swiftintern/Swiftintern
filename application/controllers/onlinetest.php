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

    public function test_participated($title, $id) {
        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();
        $test = Test::first(array("id = ?" => $id));
        
        $seo->setTitle($test->title);
        $seo->setKeywords($test->title);
        $seo->setDescription(strip_tags($test->syllabus));
        $this->getLayoutView()->set("seo", $seo);
        
        $view->set("test", $test);
        $view->set("photo", $photo);
    }

    public function certificate($uniqid) {
        $this->noview();
        $certificate = Certificate::first(array("uniqid = ?" => $uniqid));
        $participant = Participant::first(array("id = ?" => $certificate->property_id));
        $test = Test::first(array("id = ?" => $participant->test_id));
    }

}