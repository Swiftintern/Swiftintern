<?php

/**
 * Description of marketing
 *
 * @author Faizan Ayubi
 */
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Marketing extends Admin {

    /**
     * @before _secure, changeLayout
     */
    public function crmTemplate() {
        $this->seo(array("title" => "CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        if (RequestMethods::post("action") == "createCrmTemplate") {
            $body = RequestMethods::post("message");
            $subject = RequestMethods::post("subject");
            foreach ($body as $key => $value) {
                $msg = new Message(array("subject" => $subject[$key], "body" => $value));
                $msg->save();
                $message[] = $msg;
            }
            $crm = new CRM(array(
                "user_id" => $this->user->id,
                "title" => RequestMethods::post("title"),
                "first_message_id" => $message[0]->id,
                "second_message_id" => $message[1]->id
            ));
            $crm->save();
            $view->set("success", TRUE);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function crmLead() {
        $this->seo(array("title" => "Lead Generation", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $exists = array();

        if (RequestMethods::post("action") == "leadGeneration") {
            if (RequestMethods::post("emails")) {
                $emails = explode(",", RequestMethods::post("emails"));
            } else {
                $emails = array();
            }
            if (!empty($_FILES['file']['name'])) {
                $tmpName = $_FILES['file']['tmp_name'];
                $csvAsArray = array_map('str_getcsv', file($tmpName));
                foreach ($csvAsArray as $key => $value) {
                    if (strpos($value[0], "@")) {
                        $user = User::first(array("email = ?" => $value[0]), array("email"));
                        $exist = Lead::first(array("email = ?" => $value[0]), array("email"));
                        if (!$user && !$exist) {
                            array_push($emails, $value[0]);
                        } else {
                            array_push($exists, $value[0]);
                        }
                    }
                }
            }

            if (!empty($emails)) {
                $emails = array_unique($emails);
                foreach ($emails as $email) {
                    $lead = new Lead(array(
                        "user_id" => $this->user->id,
                        "email" => $email,
                        "crm_id" => RequestMethods::post("crm_id"),
                        "status" => "FIRST_MESSAGE_SENT",
                        "validity" => "1",
                        "updated" => ""
                    ));
                    $lead->save();
                }
                $crm = CRM::first(array("id = ?" => $lead->crm_id), array("first_message_id"));
                $message = Message::first(array("id = ?" => $crm->first_message_id));
                $this->notify(array(
                    "template" => "leadGeneration",
                    "subject" => $message->subject,
                    "message" => $message,
                    "user" => $this->user,
                    "from" => $this->user->name,
                    "emails" => $emails,
                    "delivery" => "mailgun"
                ));
                $view->set("success", TRUE);
            }

            if (!empty($exists)) {
                $view->set("success", implode("", $exists) . " Already Exists");
            }
        }

        $crms = CRM::all(array(), array("id", "title"));
        $view->set("crms", $crms);
    }

    /**
     * @before _secure, changeLayout
     */
    public function crmManage() {
        $this->seo(array("title" => "Manage CRM", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $leads = Lead::all(array("user_id = ?" => $this->user->id), array("*"), "created", "desc", $limit, $page);

        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("leads", $leads);
    }

    /**
     * @before _secure, changeLayout
     */
    public function newsletterCreate() {
        $this->seo(array("title" => "Create Newsletter", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "createNewsletter") {
            $message = new Message(array(
                "subject" => RequestMethods::post("subject"),
                "body" => RequestMethods::post("body")
            ));
            $message->save();

            $newsletter = new Newsletter(array(
                "message_id" => $message->id,
                "user_group" => RequestMethods::post("user_group"),
                "scheduled" => RequestMethods::post("scheduled")
            ));
            $newsletter->save();

            $view->set("success", TRUE);
        }
    }

    /**
     * @before _secure, changeLayout
     */
    public function newsletterManage() {
        $this->seo(array("title" => "Manage Newsletter", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $newsletters = Newsletter::all(array(), array("*"), "created", "desc", $limit, $page);

        $view->set("limit", $limit);
        $view->set("page", $page);
        $view->set("newsletters", $newsletters);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function sponsored() {
        $this->seo(array("title" => "Sponsored Opportunity", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        if(RequestMethods::post("action") == "sponsoreds") {
            $sponsor = new Sponsored(array(
                "opportunity_id" => RequestMethods::post("opportunity_id"),
                "user_id" => $this->user->id,
                "start" => RequestMethods::post("start"),
                "end" => RequestMethods::post("end"),
                "is_active" =>  "1",
                "validity" => "1",
                "updated" => ""
            ));
            $sponsor->save();
            $view->set("success", TRUE);
        }
        
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);
        $sponsoreds = Sponsored::all(array(), array("*"), "created", "desc", $limit, $page);
        
        $view->set("sponsoreds", $sponsoreds);
    }

}
