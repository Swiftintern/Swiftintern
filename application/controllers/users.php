<?php

/**
 * Parent controller class to test and have Common methods
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

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

    public function mailtest() {
        $this->noview();
        $this->notify(array(
            "template" => "blank",
            "subject" => "Testing email",
            "emails" => array("indianayubi@gmail.com", "faizanayubi@hotmail.com"),
            "message" => "Hi",
            "delivery" => "mailgun"
        ));
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
        return json_encode($json);
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

    protected function attachProposal($options) {
        $attachment = array();
        if (isset($options["file"])) {
            if ($options["file"] == "1") {
                $attachment = array(
                    '/home/uditverma/web/swiftintern.com/public_html/public/assets/files/proposal/Proposal.pdf',
                    '/home/uditverma/web/swiftintern.com/public_html/public/assets/files/proposal/WEB DESIGNING amp%3B HOSTING WORKSHOP CONTENT.pdf'
                );
            }
        }
        return $attachment;
    }

    protected function notify($options) {
        $body = $this->getBody($options);
        $emails = isset($options["emails"]) ? $options["emails"] : array($options["user"]->email);

        switch ($options["delivery"]) {
            case "mailgun":
                $domain = "swiftintern.com";
                $mgClient = $this->mailgun();
                $mgClient->sendMessage($domain, array(
                    'from' => "Swiftintern Team <info@swiftintern.com>",
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
                        ->setFromName("Swiftintern Team")
                        ->setSubject($options["subject"])
                        ->setHtml($body);
                $sendgrid->send($email);
                break;
        }
        $this->log(implode(",", $emails));
    }

    protected function trackUser($user) {
        $now = strftime("%Y-%m-%d %H:%M:%S", strtotime('now'));

        $user->last_login = $now;
        $user->login_number = $user->login_number + 1;
        $user->last_ip = $_SERVER['REMOTE_ADDR'];
        $user->save();
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
    
    /**
     * Redirects to external link from html views with analytics
     * @param string $url the url to redirect to
     */
    public function link($url) {
        $this->noview();
        header("Location: " . base64_decode($url));
        exit();
    }

    public function noview() {
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
    }

    public function JSONview() {
        $this->willRenderLayoutView = false;
        $this->defaultExtension = "json";
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
     * @param string $name
     * @param string $type files or images
     */
    protected function _upload($name, $type = "files") {
        if (isset($_FILES[$name])) {
            $file = $_FILES[$name];
            $path = APP_PATH . "/public/assets/uploads/{$type}/";
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
