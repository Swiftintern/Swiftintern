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

    public function test_details($title, $id) {
        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();
        $test = Test::first(array(
                    "id = ?" => $id
        ));

        $image = Image::first(array(
                    "property = ?" => "test",
                    "property_id = ?" => $id
        ));
        if ($image) {
            $photo = Photograph::first(array(
                        'id = ?' => $image->photo_id
            ));
        } else {
            $organization = Organization::first(array('id = ?' => $test->organization_id));
            $photo = Photograph::first(array('id = ?' => $organization->photo_id));
        }
        $seo->setTitle($test->title . " Details");
        $seo->setKeywords($test->title . ", online test, practice test, online exams, skills verification");
        $seo->setDescription($test->syllabus);

        $view->set("test", $test);
        $view->set("photo", $photo);
        $this->getLayoutView()->set("seo", $seo);
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

    /**
     * @before _secure
     */
    public function test($title, $id) {
        $seo = Framework\Registry::get("seo");
        $user = $this->getUser();
        $view = $this->getActionView();

        $test = Test::first(array("id = ?" => $id));
        $seo->setTitle($test->title);
        $seo->setKeywords($test->title);
        $seo->setDescription(strip_tags($test->syllabus));
        $this->getLayoutView()->set("seo", $seo);

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
        $questions = Question::all(array("test_id = ?" => $test->id));

        $participated = Participant::first(array(
                    "test_id = ?" => $test->id,
                    "user_id = ?" => $user->id
        ));

        $time = strftime("%Y-%m-%d %H:%M:%S", time());
        if ($participated) {
            $date1 = date_create($participated->created);
            $date2 = date_create($time);
            $diff = date_diff($date1, $date2);

            if ($diff->format("%a") < '15') {
                echo 'hello';
                $this->_willRenderActionView = false;
                $this->_willRenderLayoutView = false;
                self::redirect('/test-participated/' . urlencode($test->title) . '/' . $test->id);
            } else {
                $participant = new Participant(array(
                    "test_id" => $test->id,
                    "user_id" => $user->id,
                    "created" => $time
                ));
                $participant->save();
            }
        } else {
            $participant = new Participant(array(
                "test_id" => $test->id,
                "user_id" => $user->id,
                "created" => $time
            ));

            $participant->save();
        }

        $view->set("participant", $participant);
        $view->set("test", $test);
        $view->set("questions", $questions);
    }

    /**
     * @param type $test
     */
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

    /**
     * @before _secure
     * @param type $participant_id
     */
    public function test_result($participant_id) {
        //fetching participant details
        $participant = Participant::first(array('id = ?' => $participant_id));
        $user = User::first(array('id = ?' => $participant->user_id));
        $student = Student::first(array('user_id = ?' => $participant->user_id));

        //fetching test details
        $test = Test::first(array('id = ?' => $participant->test_id));

        $seo = Framework\Registry::get("seo");
        $view = $this->getActionView();

        $seo->setTitle("Result of {$test->title} by {$user->name}");
        $seo->setKeywords("test result");
        $seo->setDescription("Result of {$test->title} by {$user->name}");
        $this->getLayoutView()->set("seo", $seo);

        $view->set("user", $user);
        $view->set("participant", $participant);
        $view->set("student", $student);
        $view->set("test", $test);
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
