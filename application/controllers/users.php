<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Users extends Controller {

    public function login() {
        $seo = Registry::get("seo");

        $seo->setTitle("Login");
        $seo->setKeywords("login, signin, students account login, employer account login");
        $seo->setDescription("Login to your account on swiftintern, students login to apply for internship and employer login to hire interns.");

        $this->getLayoutView()->set("seo", $seo);
        
        $session = Registry::get("session");
        $user = $this->user;

        if (!empty($user)) {
            self::redirect($user->type."s/profile");
        }

        if (RequestMethods::post("action") == "login") {
            $email = RequestMethods::post("email");
            $password = RequestMethods::post("password");

            $view = $this->getActionView();
            $error = false;

            if (empty($email)) {
                $view->set("email_error", "Email not provided");
                $error = true;
            }

            if (empty($password)) {
                $view->set("password_error", "Password not provided");
                $error = true;
            }

            if (!$error) {
                $user = User::first(array(
                    "email = ?" => $email,
                    "password = ?" => sha1($password),
                    "validity = ?" => true
                ));

                if (!empty($user)) {
                    $this->user = $user;
                    switch ($user->type) {
                        case "student":
                            $student = Student::first(array(
                                "user_id = ?" => $user->id
                            ));
                            if (!empty($student)) {
                                $session->set("student", $student);
                            }
                            self::redirect("/students/profile");
                            break;
                        case "employer":
                            $member = Member::all(
                                array(
                                    "user_id = ?" => $user->id,
                                    "validity = ?" => true
                                ),
                                array("id", "organization_id", "designation", "authority")
                            );
                            
                            $membersof = array();
                            foreach($member as $mem){
                                $organization = Organization::first(
                                    array("id = ?" => $mem->organization_id),
                                    array("id", "name", "photo_id")
                                );
                                $membersof[] = array(
                                    "id" => $mem->id,
                                    "organization" => $organization,
                                    "designation" => $mem->designation,
                                    "authority" => $mem->authority
                                );
                            }
                            
                            $employer = \Framework\ArrayMethods::toObject($membersof[0]);
                            if (!empty($employer)) {
                                $session->set("member", \Framework\ArrayMethods::toObject($membersof));
                                $session->set("employer", $employer);
                                self::redirect("/employer");
                            } else {
                                self::redirect("/users/blocked");
                            }
                            break;
                    }
                } else {
                    $view->set("password_error", "Email address and/or password are incorrect");
                }
            }
        }
    }
    
    public function blocked() {
        $this->setUser(false);
        $this->seo(array(
            "title" => "Blocked",
            "keywords" => "",
            "description" => "",
            "view" => $this->getLayoutView()
        ));
    }

    public function logout() {
        $this->setUser(false);
        self::redirect("/users/login.html");
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

}