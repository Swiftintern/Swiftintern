<?php

/**
 * Description of ambassador
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class Ambassador extends Students {

    /**
     * Stats for ambassador
     * 
     * @before _secure, changeLayout
     */
    public function index() {
        $this->seo(array("title" => "Campus Ambassadors", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
    }

    /**
     * @before _secure, changeLayout
     */
    public function generateId() {
        $this->seo(array("title" => "Generate Id Campus ambassador", "keywords" => "Ambassadors", "description" => "Be a campus hero by being swiftintern student partner", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $organizations = array();

        $qualifications = Qualification::all(array("student_id = ?" => $this->student->id), array("organization_id"));
        foreach ($qualifications as $qualification) {
            $organizations[] = Organization::first(array("id = ?" => $qualification->organization_id), array("name"));
        }

        if (RequestMethods::post("file")) {
            $filename = $this->_upload("file", "images");
            $college = RequestMethods::post("college");
            $view->set("success", TRUE);
            $view->set("filename", $filename);
            $view->set("college", $college);
        }
        
        $view->set("organizations", $organizations);
    }
    
    public function createId($college, $photo = "sp.png") {
        $this->noview();

        $im = imagecreatefromjpeg(APP_PATH . '/public/assets/images/others/ssp.jpg');
        $src = imagecreatefrompng(APP_PATH . "/public/assets/uploads/images/{$photo}");
        $black = imagecolorallocate($im, 0x00, 0x00, 0x00);
        $times = APP_PATH . '/public/assets/fonts/times.ttf';
        
        //merge two images (profile picture on id card image template)
        imagecopymerge($im, $src, 82, 256, 0, 0, 265, 265, 100);

        // Draw the text
        imagettftext($im, 30, 0, 430, 345, $black, $times, $this->user->name);
        imagettftext($im, 30, 0, 430, 410, $black, $times, $college);
        imagettftext($im, 30, 0, 450, 515, $black, $times, $this->user->id);

        // Output image to the browser
        header('Content-Type: image/png');

        imagepng($im);
        imagedestroy($im);
    }

}
