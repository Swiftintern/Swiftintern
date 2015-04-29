<?php

/**
 * Description of home
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Home extends Users {

    public function index() {
        $view = $this->getActionView();

        $query = RequestMethods::get("query", "");
        $location = RequestMethods::get("location", "");
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);

        $where = array(
            "title LIKE ?" => "%{$query}%",
            "category LIKE ?" => "%{$query}%",
            "location LIKE ?" => "%{$location}%",
            "validity = ?" => true
        );

        $fields = array("id", "title", "eligibility", "location", "last_date", "organization_id");

        $count = Opportunity::count($where);
        $opportunities = Opportunity::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("limit", $limit);
        $view->set("count", count($opportunities));
        $view->set("opportunities", $opportunities);

        $this->getLayoutView()->set("seo", Framework\Registry::get("seo"));
    }

    public function linkedins() {
        $li = Framework\Registry::get("linkedin");

        $url = $li->getLoginUrl(array(
            LinkedIn::SCOPE_FULL_PROFILE,
            LinkedIn::SCOPE_EMAIL_ADDRESS,
            LinkedIn::SCOPE_CONTACT_INFO
        ));

        if (isset($_REQUEST['code'])) {
            $token = $li->getAccessToken($_REQUEST['code']);
            $token_expires = $li->getAccessTokenExpiration();
        }

        if ($li->hasAccessToken()) {
            $info = $li->get('/people/~:(first-name,last-name,positions,email-address,public-profile-url,location,picture-url,educations,skills,phone-numbers)');
            echo "<pre>", print_r($info), "</pre>";
        } else {
            header("Location: {$url}");
            exit();
        }
    }

    public function about() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("About Us");
        $seo->setKeywords("about us, how swiftintern works, swiftintern");
        $seo->setDescription("SwiftIntern is the india's largest student-focused internship portal, bringing students, employers and higher education institutions together in one centralized location.");
        $seo->setPhoto("http://assets.swiftintern.com/img/newsletter/header.png");

        $this->getLayoutView()->set("seo", $seo);
    }
    
    public function support() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Suppprt");
        $seo->setKeywords("support, faq, frequently asked Questions");
        $seo->setDescription("See the answer related to problems on internship and hiring interns");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function contact() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Contact Us");
        $seo->setKeywords("contact, report problem, swiftintern");
        $seo->setDescription("We would love to hear from you. contact us to know more.");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function privacy() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Privacy Policy");
        $seo->setKeywords("privacy policy");
        $seo->setDescription("We collect information from you when you register on our site, place an order, subscribe to our newsletter, respond to a survey or fill out a form. ");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function blog() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Intern Blog");
        $seo->setKeywords("blog, Intern Blog, internship tips, internship advice, internship discussions");
        $seo->setDescription("Internship blogs post tips, advice to students to achieve the most from their internship and how to avail maximum benefits during an intern period.");

        $this->getLayoutView()->set("seo", $seo);
        $view = $this->getActionView();

        $query = RequestMethods::get("query", "");
        $order = RequestMethods::get("order", "created");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 10);

        $where = array(
            "title LIKE ?" => "%{$query}%",
            "category LIKE ?" => "%{$query}%",
            "validity = ?" => true
        );

        $fields = array("id", "title", "content", "category", "created");

        $count = BlogPost::count($where);
        $posts = BlogPost::all($where, $fields, $order, $direction, $limit, $page);

        $view->set("count", $count);
        $view->set("posts", $posts);
    }

    public function post($title, $id) {
        $seo = Framework\Registry::get("seo");

        $post = BlogPost::first(
                        array("id = ?" => $id), array("id", "title", "content", "category", "created")
        );

        $seo->setTitle($post->title);
        $seo->setKeywords($post->category);
        $seo->setDescription(substr(strip_tags($post->content), 0, 150));

        $this->getLayoutView()->set("seo", $seo);
        $this->getActionView()->set("post", $post);
    }

    public function termsofservice() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Terms of Service");
        $seo->setKeywords("terms of use, refund policy, swiftintern");
        $seo->setDescription("Following is the agrrement of use on swiftintern including refund policy.");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function sponsored() {
        global $datetime;
        $this->seo(array(
            "title" => "Get Internship | Student Register",
            "keywords" => "get internship, student register",
            "description" => "Register with us to get internship from top companies in india and various startups in Delhi, Mumbai, Bangalore, Chennai, hyderabad etc",
            "view" => $this->getLayoutView()
        ));
        $sponsoreds = array();

        $order = RequestMethods::get("order", "id");
        $direction = RequestMethods::get("direction", "desc");
        $page = RequestMethods::get("page", 1);
        $limit = RequestMethods::get("limit", 1);

        $where = array(
            "start <= ?" => $datetime->format("Y-m-d"),
            "end >= ?" => $datetime->format("Y-m-d"),
            "validity = ?" => true,
            "is_active = ?" => true
        );

        $fields = array("opportunity_id");

        $sponsored = Sponsored::all($where, $fields, $order, $direction, $limit, $page);
        foreach ($sponsored as $sd) {
            $sponsoreds = Opportunity::all(
                            array(
                        "id = ?" => $sd->opportunity_id
                            ), array("id", "title", "location", "last_date", "eligibility")
            );
        }
        $this->getActionView()->set("sponsoreds", $sponsoreds);
    }

    public function opportunity($title, $id) {
        $view = $this->getActionView();
        $opportunity = Opportunity::first(array("id = ?" => $id));

        self::redirect('/' . $opportunity->type . '/' . urlencode($title) . '/' . $id);

        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "view" => $this->getLayoutView()
        ));

        $view->set("opportunity", $opportunity);
    }
    
    function application() {
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
        $application = new Application(array(
            "student_id" => RequestMethods::post("student_id", "1"),
            "opportunity_id" => RequestMethods::post("opportunity_id", "363"),
            "property_id" => RequestMethods::post("property_id", "204"),
            "status" => RequestMethods::post("status", "applied"),
            "updated" => RequestMethods::post("updated", "")
        ));

        $application->save();
        echo '<pre>', print_r($application),'</pre>';
    }

    public function internship($title, $id) {
        global $datetime;
        $view = $this->getActionView();
        $session = Registry::get("session");
        $student = $session->get("student");

        $opportunity = Opportunity::first(array("id = ?" => $id));
        $organization = Organization::first(array("id = ?" => $opportunity->organization_id), array("id", "name"));
        if($student) {
            $application = Application::first(array("student_id = ?" => $student->id, "opportunity_id = ?" => $id));
            $view->set("application", $application);
        }

        if (RequestMethods::post("action") == "application") {
            $application = new Application(array(
                "student_id" => RequestMethods::post("student_id", $student->id),
                "opportunity_id" => RequestMethods::post("opportunity_id"),
                "property_id" => RequestMethods::post("resume_id", ""),
                "status" => RequestMethods::post("status", "applied"),
                "updated" => RequestMethods::post("updated", "")
            ));

            $application->save();
            $view->set("application", $application);
        }

        $this->seo(array(
            "title" => $opportunity->title,
            "keywords" => $opportunity->category . ', ' . $opportunity->location,
            "description" => substr(strip_tags($opportunity->details), 0, 150),
            "view" => $this->getLayoutView()
        ));

        $view->set("enddate", $datetime->format("Y-m-d"));
        $view->set("opportunity", $opportunity);
        $view->set("organization", $organization);
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
	
	public function spoj() {
        $view = $this->getActionView();
		
		$seo = Framework\Registry::get("seo");
        $seo->setTitle("Spoj User");
        $seo->setKeywords("swiftintern");
        $seo->setDescription("find details of any spoj user.");
		
        $user = "viplov";
        $spoj = new Spoj(array('username' => $user));
        
        if($spoj->isValid) {

            $view->set("user", $user);
            $view->set("joined", $spoj->getJoined());
            $view->set("institution", $spoj->getSchool());
            $view->set("rank", $spoj->getRank());
            $view->set("problem", $spoj->getProbSolved());
        } else {
            $view->set("error", "Could find the details for given user");
        }
    }

    public function codechef() {
        $view = $this->getActionView();
		
		$seo = Framework\Registry::get("seo");
        $seo->setTitle("Codechef User");
        $seo->setKeywords("swiftintern");
        $seo->setDescription("find details of any codechef user.");
		
        $user = "ashish1610";
        $codechef = new CodeChef(array('username' => $user));
        
        if($codechef->isValid) {

            $view->set("user", $user);
            $view->set("name", $codechef->getName());
            $view->set("details", $codechef->getDetails());
            $view->set("rank", $codechef->getRank());
        } else {
            $view->set("error", "Could find the details for given user");
        }
    }
}
