<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Student extends Controller {

    /**
     * Does three important things, first is retrieving the posted form data, second is checking each form field’s value
     * third thing it does is to create a new user row in the database
     */
    public function register() {
        include APP_PATH .'/public/datalist.php';
        $view = $this->getActionView();
        
        $view->set("alldegrees", $alldegrees);
        $view->set("allmajors", $allmajors);
        $view->set("alllocations", $alllocations);
        
        $view->set("errors", array());

        if (RequestMethods::post("register")) {
            $user = new User(array(
                "first" => RequestMethods::post("first"),
                "last" => RequestMethods::post("last"),
                "email" => RequestMethods::post("email"),
                "password" => RequestMethods::post("password")
            ));

            if ($user->validate()) {
                $user->save();
                $this->_upload("photo", $user->id);
                $view->set("success", true);
            }

            $view->set("errors", $user->getErrors());
        }
    }

    public function profile() {
        $session = Registry::get("session");
        $user = $this->user;

        if (empty($user)) {
            $user = new StdClass();
            $user->first = "Mr.";
            $user->last = "Smith";
            $user->file = "";
        }

        $this->getActionView()->set("user", $user);
    }

    public function settings() {
        $view = $this->getActionView();
        $user = $this->getUser();

        if (RequestMethods::post("update")) {
            $user = new User(array(
                "first" => RequestMethods::post("first", $user->first),
                "last" => RequestMethods::post("last", $user->last),
                "email" => RequestMethods::post("email", $user->email),
                "password" => RequestMethods::post("password", $user->password)
            ));

            if ($user->validate()) {
                $user->save();
                $this->user = $user;
                $this->_upload("photo", $this->user->id);
                $view->set("success", true);
            }

            $view->set("errors", $user->getErrors());
        }
    }

    /**
     * The method checks whether a file has been uploaded. If it has, the method attempts to move the file to a permanent location.
     * @param type $name
     * @param type $user
     */
    protected function _upload($name, $user) {
        if (isset($_FILES[$name])) {
            $file = $_FILES[$name];
            $path = APP_PATH . "/public/uploads/";
            $time = time();
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            $filename = "{$user}-{$time}.{$extension}";
            if (move_uploaded_file($file["tmp_name"], $path . $filename)) {
                $meta = getimagesize($path . $filename);
                if ($meta) {
                    $width = $meta[0];
                    $height = $meta[1];
                    $file = new File(array(
                        "name" => $filename,
                        "mime" => $file["type"],
                        "size" => $file["size"],
                        "width" => $width,
                        "height" => $height,
                        "user" => $user
                    ));
                    $file->save();
                }
            }
        }
    }

    public function edit($id) {
        $errors = array();

        $user = User::first(array(
                    "id = ?" => $id
        ));

        if (RequestMethods::post("save")) {
            $user->first = RequestMethods::post("first");
            $user->last = RequestMethods::post("last");
            $user->email = RequestMethods::post("email");
            $user->password = RequestMethods::post("password");
            $user->live = (boolean) RequestMethods::post("live");
            $user->admin = (boolean) RequestMethods::post("admin");

            if ($user->validate()) {
                $user->save();
                $this->actionView->set("success", true);
            }

            $errors = $user->errors;
        }

        $this->actionView
                ->set("user", $user)
                ->set("errors", $errors);
    }
}