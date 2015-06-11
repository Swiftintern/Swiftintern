<?php

/**
 * Parent controller class to test and have Common methods
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
        $body = $view->render();$emails = array();
        if(isset($options["emails"])){
            $emails = $options["emails"];
        } else{
            array_push($emails, $user->email);
        }
        
        if(isset($options["from"])){
            $from = $options["from"];
        } else{
            $from = "Saud Akhtar";
        }

        switch ($options["delivery"]) {
            case "mailgun":
                $domain = "swiftintern.com";
                $mgClient = $this->mailgun();
                # Make the call to the client.
                $result = $mgClient->sendMessage($domain, array(
                    'from' => "{$from} <info@swiftintern.com>",
                    'to' => $options["emails"],
                    'subject' => $options["subject"],
                    'html' => $body
                ));
                break;
            default:
                $sendgrid = $this->sendgrid();
                $email = new \SendGrid\Email();
                $email->setSmtpapiTos($emails)
                        ->setFrom('info@swiftintern.com')
                        ->setFromName($from)
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

    public function log($message = "") {
        $logfile = APP_PATH . "/logs/" . date("Y-m-d") . ".txt";
        $new = file_exists($logfile) ? false : true;
        if ($handle = fopen($logfile, 'a')) {
            $timestamp = strftime("%Y-%m-%d %H:%M:%S", time() + 1800);
            $content = "[{$timestamp}]{$message}\n";
            fwrite($handle, $content);
            fclose($handle);
            if ($new) {
                chmod($logfile, 0755);
            }
        } else {
            echo "Could not open log file for writing";
        }
    }
    
    public function track($property, $property_id) {
        header( 'Content-Type: image/png' );
        
        Stat::log($property, $property_id);
        $pixel = 'http://assets.swiftintern.com/images/others/track.png';
        
        //Get the filesize of the image for headers
        $filesize = filesize(APP_PATH . '/assets/images/others/track.png');
    
        //Now actually output the image requested, while disregarding if the database was affected
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Disposition: attachment; filename="pixel.png"');
        header('Content-Transfer-Encoding: binary' );
        header('Content-Length: '.$filesize);
        readfile($pixel);
        
        exit;
    }

    /**
     * The method checks whether a file has been uploaded. If it has, the method attempts to move the file to a permanent location.
     * @param type $name
     * @param type $user
     */
    protected function _upload($name) {
        if (isset($_FILES[$name])) {
            $file = $_FILES[$name];
            $path = APP_PATH . "/public/assets/uploads/files/";
            $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
            echo $filename = uniqid().".{$extension}";
            if (move_uploaded_file($file["tmp_name"], $path . $filename)) {
                return $filename;
            } else {
                return FALSE;
            }
        }
    }

}
