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

    protected function access($info = array()) {
        if (!empty($info)) {
            $user = $this->_checkitem(array(
                "model" => "user",
                "where" => array(
                    "email = ?" => $info["emailAddress"],
                )
            ));
            if ($user) {
                $session = Registry::get("session");
                $session->set("pictureUrl", $info["pictureUrl"]);
                
                $this->createSession($user);
            }
            return TRUE;
        }
        return FALSE;
    }

    protected function createSession($user) {
        $this->user = $user;
        $session = Registry::get("session");

        switch ($user->type) {
            case "student":
                $student = Student::first(array("user_id = ?" => $user->id));
                if (!empty($student)) { $session->set("student", $student);}
                self::redirect("/students");
                break;
            case "employer":
                $member = Member::all(array("user_id = ?" => $user->id, "validity = ?" => true), array("id", "organization_id", "designation", "authority"));

                $membersof = array();
                foreach ($member as $mem) {
                    $organization = Organization::first(array("id = ?" => $mem->organization_id), array("id", "name", "photo_id", "linkedin_id"));
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
    }
    
    protected function newUser($info = array()) {
        if ($info["phoneNumbers"]["_total"] > 0) { $phone = $info["phoneNumbers"]["values"]["0"]["phoneNumber"];}
        else { $phone = "";}
        $user = new User(array(
            "name" => $info["firstName"] . " " . $info["lastName"],
            "email" => $info["emailAddress"],
            "phone" => $phone,
            "password" => rand(100000, 99999999),
            "access_token" => rand(100000, 99999999),
            "type" => $info["type"],
            "validity" => "1",
            "last_ip" => $_SERVER['REMOTE_ADDR'],
            "last_login" => "",
            "updated" => ""
        ));$user->save();
        
        $social = new Social(array(
            "user_id" => $user->id,
            "social_platform" => "linkedin",
            "link" => $info["publicProfileUrl"]
        ));$social->save();
        
        return $user;
    }
    
    public function login() {
        $seo = Registry::get("seo");

        $seo->setTitle("Login");
        $seo->setKeywords("login, signin, students account login, employer account login");
        $seo->setDescription("Login to your account on swiftintern, students login to apply for internship and employer login to hire interns.");

        $this->getLayoutView()->set("seo", $seo);

        $session = Registry::get("session");
        $user = $this->user;
        if (!empty($user)) {
            self::redirect(Framework\StringMethods::plural($user->type));
        }

        $li = Framework\Registry::get("linkedin");
        $redirect = RequestMethods::get("redirect", "");
        if (!empty($redirect)) {
            $li->changeCallbackURL($redirect);
        }

        $url = $li->getLoginUrl(array(
            LinkedIn::SCOPE_FULL_PROFILE,
            LinkedIn::SCOPE_EMAIL_ADDRESS,
            LinkedIn::SCOPE_CONTACT_INFO
        ));
        $this->getActionView()->set("url", $url);

        if (isset($_REQUEST['code'])) { $token = $li->getAccessToken($_REQUEST['code']);}

        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills,phone-numbers)');
            if ($this->access($info)) {
                self::redirect("/" . $user->type);
            } else {
                self::redirect("/students/register");
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
        self::redirect("/home");
    }
    
    protected function LinkedIn($redirect = "") {
        $li = Framework\Registry::get("linkedin");
        
        if(!empty($redirect)){
            $li->changeCallbackURL($redirect);
        }
        
        return $li;
    }

}
