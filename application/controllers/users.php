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
     * Checks for set URL and redirects them
     * @param string $url the url to redirect to
     */
    public function setRedirect($url = NULL) {
        $session = Registry::get("session");
        $storedUrl = $session->get('url');
        // If url is given then set the url in session
        if ($url) {
            $session->set("url", $url);
        } elseif ($storedUrl) {  // url not given but set in session
            // Then check if we reached the destination
            if ($_SERVER[REQUEST_URI] == urldecode($storedUrl)) {
                $session->erase("url");
            } else {
                // We need to redirect them to stored location
                self::redirect($storedUrl);
            }
        }
    }

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

    public function mailtest() {
        $this->noview();
        $options = array(
            "template" => "blank",
            "subject" => "Testing email",
            "emails" => array("indianayubi@gmail.com", "faizanayubi@hotmail.com"),
            "delivery" => "mailgun"
        );
        $this->notify($options);
    }

    public function unsubscribe() {
        $this->noview();
        echo '<h1>Unsubscribed Successfully</h1>';
    }
    
    protected function recipientVariables($emails) {
        $json = array();
        foreach ($emails as $email) {
            $json[$email] = array(
                "cat" => "newsletter"
            );
        }
        
        return json_encode($json, JSON_PRETTY_PRINT);
    }

    protected function getBody($options) {
        $template = $options["template"];
        $view = new Framework\View(array(
            "file" => APP_PATH . "/application/views/users/emails/{$template}.html"
        ));
        foreach ($options as $key => $value) {
            $view->set($key, $value);
            $$key = $value;
        }

        return $view->render();
    }

    protected function notify($options) {
        $body = $this->getBody($options);
        $emails = isset($options["emails"]) ? $options["emails"] : [$options["user"]->email];
        $from = isset($options["from"]) ? $options["from"] : "Saud Akhtar";

        switch ($options["delivery"]) {
            case "mailgun":
                $domain = "swiftintern.com";
                $mgClient = $this->mailgun();
                $mgClient->sendMessage($domain, array(
                    'from' => "{$from} <info@swiftintern.com>",
                    'to' => implode(",", $emails),
                    'subject' => $options["subject"],
                    'html' => $body,
                    'recipient-variables' => $this->recipientVariables($emails)
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
        $this->log(implode(",", $emails));
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

    protected function log($message = "") {
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
        header('Content-Type: image/png');

        Stat::log($property, $property_id);
        $pixel = 'http://assets.swiftintern.com/images/others/track.png';

        //Get the filesize of the image for headers
        $filesize = filesize(APP_PATH . '/public/assets/images/others/track.png');

        //Now actually output the image requested, while disregarding if the database was affected
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Disposition: attachment; filename="pixel.png"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
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
            $filename = uniqid() . ".{$extension}";
            if (move_uploaded_file($file["tmp_name"], $path . $filename)) {
                return $filename;
            } else {
                return FALSE;
            }
        }
    }

}
