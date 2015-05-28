<?php

/**
 * Description of users
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;

class Users extends Controller {
    
    /**
     * The Main Method to return SendGrid Instance
     * 
     * @return \SendGrid\SendGrid Instance of Sendgrid
     */
    protected function sendgrid() {
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/mail");

        if (!empty($parsed->mail->sendgrid) && !empty($parsed->mail->sendgrid->username)) {
            $sendgrid = new \SendGrid\SendGrid($parsed->mail->sendgrid->username, $parsed->mail->sendgrid->password);
            return $sendgrid;
        }
    }
    
    /**
     * The Main Method to return MailGun Instance
     * 
     * @return \Mailgun\Mailgun Instance of MailGun
     */
    protected function mailgun() {
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/mail");

        if (!empty($parsed->mail->mailgun)) {
            $mailgun = new \Mailgun\Mailgun($parsed->mail->mailgun->key);
            return $mailgun;
        }
    }
    
    protected function mailtest() {
        $this->noview();
        $options = array(
            "template" => "studentRegister",
            "subject" => "Getting Started on Swiftintern.com",
            "user" => User::first(array("id = ?" => "31"))
        );
        $this->notify($options);
    }
    
    protected function notify($options) {
        $template = $options["template"];
        $view = new Framework\View(array(
            "file" => APP_PATH . "/application/views/users/emails/{$template}.html"
        ));
        foreach ($options as $key => $value) {
            $view->set($key, $value);
            $$key = $value;
        }
        $body = $view->render();
        
        switch ($options["delivery"]) {
            case "mailgun":
                break;
            default:
                $sendgrid = $this->sendgrid();
                $email = new \SendGrid\Email();
                $email->addTo($user->email)
                    ->setFrom('info@swiftintern.com')
                    ->setFromName('Saud Akhtar')
                    ->setSubject($options["subject"])
                    ->setHtml($body);
                $sendgrid->send($email);
                break;
        }
    }

    public function logout() {
        $this->setUser(false);
        self::redirect("/home");
    }

    protected function LinkedIn($redirect = "") {
        $li = Framework\Registry::get("linkedin");
        if (!empty($redirect)) {
            $li->changeCallbackURL($redirect);
        }
        return $li;
    }

    public function noview() {
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
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