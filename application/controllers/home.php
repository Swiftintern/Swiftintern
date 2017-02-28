<?php

/**
 * Controller to all Public Request such as contact, about etc
 *
 * @author Faizan Ayubi
 */
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
            "location LIKE ?" => "%{$location}%",
            "type = ?" => "internship",
            "validity = ?" => true
        );

        $fields = array("id", "title", "eligibility", "location", "last_date", "organization_id", "type");

        $count = Opportunity::count($where);
        $opportunities = Opportunity::all($where, $fields, $order, $direction, $limit, $page);
        
        $testimonials = Experience::all(array("organization_id = ?" => "1574"), array("details", "user_id"));
        $testicount = count($testimonials);
        $rand = rand(0, $testicount);
        $tuser = User::first(array("id = ?" => $testimonial->user_id), array("name"));

        $view->set("limit", $limit);
        $view->set("count", $count);
        $view->set("opportunities", $opportunities);
        
        $view->set("tuser", $tuser);
        $view->set("testimonial", $testimonials[$rand]);

        $this->getLayoutView()->set("seo", Framework\Registry::get("seo"));
    }

    public function about() {
        $this->seo(array(
            "title" => "About Us",
            "keywords" => "about us, how swiftintern works, swiftintern",
            "description" => "SwiftIntern is the india's largest student-focused internship portal, bringing students, employers and higher education institutions together in one centralized location.",
            "photo" => "http://swiftintern.com/public/assets/img/newsletter/header.png",
            "view" => $this->getLayoutView()
        ));
    }

    public function support() {
        $this->seo(array(
            "title" => "Suppprt",
            "keywords" => "support, faq, frequently asked Questions",
            "description" => "See the answer related to problems on internship and hiring interns",
            "view" => $this->getLayoutView()
        ));
    }

    public function contact() {
        $this->seo(array(
            "title" => "Contact Us",
            "keywords" => "contact, report problem, swiftintern",
            "description" => "We would love to hear from you. contact us to know more.",
            "view" => $this->getLayoutView()
        ));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "contact") {
            $errors = array();
            $fields = array("name", "email", "message");
            foreach ($fields as $key => $value) {
                $$value = RequestMethods::post($value);
                if (empty($$value)) {
                    $errors[$value] = "** ".ucfirst($value). " is required!!";
                }
            }

            if (empty($errors)) {
                mail("udit@swiftintern.com", "Contact Query By: $name", "Email: $email\r\n".$message);
                $view->set("success", true);    
            } else {
                $view->set("errors", $errors);
            }
        }
    }

    public function privacy() {
        $this->seo(array(
            "title" => "Privacy Policy",
            "keywords" => "privacy policy",
            "description" => "We collect information from you when you register on our site, place an order, subscribe to our newsletter, respond to a survey or fill out a form.",
            "view" => $this->getLayoutView()
        ));
    }
    
    public function partners() {
        $this->seo(array(
            "title" => "Testimonials Swiftintern",
            "keywords" => "testimonials",
            "description" => "See why we are best, and how happy people are with us",
            "view" => $this->getLayoutView()
        ));
    }

    public function blog() {
        $this->seo(array(
            "title" => "Intern Blog",
            "keywords" => "blog, Intern Blog, internship tips, internship advice, internship discussions",
            "description" => "Internship blogs post tips, advice to students to achieve the most from their internship and how to avail maximum benefits during an intern period.",
            "view" => $this->getLayoutView()
        ));
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
        $view = $this->getActionView();
        
        $post = BlogPost::first(array("id = ?" => $id), array("id", "title", "content", "category", "created"));
        $image = Image::first(array("property = ?" => "post", "property_id = ?" => $post->id), array("photo_id"));
        if ($image) {
            $view->set("image", $image);
        }
        
        $this->seo(array(
            "title" => $post->title,
            "keywords" => $post->category,
            "description" => substr(strip_tags($post->content), 0, 150),
            "view" => $this->getLayoutView()
        ));
        $view->set("post", $post);
    }
    
    /**
     * @before _secure
     */
    public function saveBlogPost($id = NULL) {
        if($id != NULL){
            $post = BlogPost::first(array("id = ?" => $id), array("id", "title", "content", "category", "created"));
        } else {
            $post = new BlogPost();
            $post->user_id = $this->user->id;
            $post->validity = "0";
        }
        
        if (RequestMethods::post("action") == "post") {
            $post->title = RequestMethods::post("title");
            $post->content = RequestMethods::post("content");
            $post->category = RequestMethods::post("category", "education");
            $post->updated = "";
            $post->save();
            
            $photo = new Photograph(array(
                "filename" => $this->_upload("photo", "images"),
                "type" => $_FILES['photo']['type'],
                "size" => $_FILES['photo']['size']
            ));
            $photo->save();
            
            $image = new Image(array(
                "photo_id" => $photo->id,
                "user_id" => $this->user->id,
                "property" => "post",
                "property_id" => $post->id
            ));
            $image->save();
            
            $this->getActionView()->set("success", true);
        }
        
        $this->seo(array(
            "title" => "Intern Blog Post",
            "keywords" => "intern blog",
            "description" => "Get Your work published as an intern",
            "view" => $this->getLayoutView()
        ));
        $this->getActionView()->set("post", $post);
    }

    public function termsofservice() {
        $seo = Framework\Registry::get("seo");

        $seo->setTitle("Terms of Service");
        $seo->setKeywords("terms of use, refund policy, swiftintern");
        $seo->setDescription("Following is the agrrement of use on swiftintern including refund policy.");

        $this->getLayoutView()->set("seo", $seo);
    }

    public function thumbnails($id, $width = 64, $height = 64) {
        $path = APP_PATH . "/public/assets/uploads/images";
        $cdn = CDN;
        $file = Photograph::first(array("id = ?" => $id));
        if ($file) {
            $name = $file->filename;
            $filename = pathinfo($name, PATHINFO_FILENAME);
            $extension = pathinfo($name, PATHINFO_EXTENSION);

            if ($filename && $extension) {
                $thumbnail = "{$filename}-{$width}x{$height}.{$extension}";
                if (!file_exists("{$path}/{$thumbnail}")) {
                    $imagine = new \Imagine\Gd\Imagine();
                    $size = new \Imagine\Image\Box($width, $height);
                    $mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
                    $imagine->open("{$path}/{$name}")->thumbnail($size, $mode)->save("{$path}/thumbnails/{$thumbnail}");
                }
                header("Location: {$cdn}uploads/images/thumbnails/{$thumbnail}");
                exit();
            }
            header("Location: /images/{$name}");
            exit();
        } else {
            header("Location: {$cdn}images/logo.png");
            exit();
        }
    }

    public function success() {
        $this->seo(array(
            "title" => "Payment Successful",
            "keywords" => "payment blog",
            "description" => "Payment successfully done",
            "view" => $this->getLayoutView()
        ));
    }

}
