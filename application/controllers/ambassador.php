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
        $organizations = array();$photo = "";

        $qualifications = Qualification::all(array("student_id = ?" => $this->student->id), array("organization_id"));
        foreach ($qualifications as $qualification) {
            $organizations[] = Organization::first(array("id = ?" => $qualification->organization_id), array("name"));
        }
        $image = Image::first(array("property = ?" => "user", "property_id = ?" => $this->user->id));
        if($image){
            $photo = Photograph::first(array("id = ?" => $image->photo_id));
        }
        
        if (RequestMethods::post("action") == "generateId") {
            $photo = new Photograph(array(
                "filename" => $this->_upload("file", "images"),
                "type" => "",
                "size" => ""
            ));
            $photo->save();
            
            $image = new Image(array(
                "photo_id" => $photo->id,
                "user_id" => $this->user->id,
                "property" => "user",
                "property_id" => $this->user->id
            ));
            $image->save();
            
            $college = RequestMethods::post("college");
            $view->set("success", TRUE);
            $view->set("college", $college);
        }
        
        $view->set("photo", $photo);
        $view->set("organizations", $organizations);
    }
    
    public function createId($college, $photoId) {
        $this->noview();
        $photo = Photograph::first(array("id = ?" => $photoId));
        
        $im = imagecreatefromjpeg(APP_PATH . '/public/assets/images/others/ssp.jpg');
        $ext = explode(".",$photo->filename);
        switch (end($ext)) {
            case "jpg":
                $src = imagecreatefromjpeg(APP_PATH . "/public/assets/uploads/images/{$photo->filename}");
                break;
            
            case "png":
                $src = imagecreatefrompng(APP_PATH . "/public/assets/uploads/images/{$photo->filename}");
                break;

            default:
                $src = imagecreatefromjpeg(APP_PATH . "/public/assets/uploads/images/{$photo->filename}");
                break;
        }

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
