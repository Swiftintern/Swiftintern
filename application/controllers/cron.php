<?php

/**
 * Scheduler Class which executes daily and perfoms the initiated job
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class CRON extends Controller {

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
    }

    /**
     * @before _secure
     */
    public function index() {
        $this->notifications();
    }

    protected function newsletters() {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $newsletters = Newsletter::all(array("scheduled = ?" => $now));
        foreach ($newsletters as $newsletter) {
            $users = User::first();
        }
    }

    protected function notifications() {
        $yesterday = strftime("%Y-%m-%d", strtotime('-1 day'));
        $applications = Application::all(array("updated = ?" => $yesterday), array("id", "student_id", "opportunity_id", "status"));
        echo count($applications);
    }

    public function trackImage($property, $property_id) {
        
    }

    /**
     * @protected
     */
    public function _secure() {
        //echo php_sapi_name();
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
            die('access is not permitted');
        }
    }

}
