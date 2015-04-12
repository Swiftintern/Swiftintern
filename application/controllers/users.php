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
                            break;
                    }
                    self::redirect($user->type."s/profile");
                } else {
                    $view->set("password_error", "Email address and/or password are incorrect");
                }
            }
        }
    }

    public function search() {
        $view = $this->getActionView();

        $query = RequestMethods::post("query");
        $order = RequestMethods::post("order", "modified");
        $direction = RequestMethods::post("direction", "desc");
        $page = RequestMethods::post("page", 1);
        $limit = RequestMethods::post("limit", 10);

        $count = 0;
        $users = false;

        if (RequestMethods::post("search")) {
            $where = array(
                "SOUNDEX(first) = SOUNDEX(?)" => $query,
                "live = ?" => true,
                "deleted = ?" => false
            );

            $fields = array(
                "id", "first", "last"
            );

            $count = User::count($where);
            $users = User::all($where, $fields, $order, $direction, $limit, $page);
        }

        $view
                ->set("query", $query)
                ->set("order", $order)
                ->set("direction", $direction)
                ->set("page", $page)
                ->set("limit", $limit)
                ->set("count", $count)
                ->set("users", $users);
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