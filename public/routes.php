<?php

// define routes

$routes = array(
    array(
        "pattern" => "student/register",
        "controller" => "students",
        "action" => "register"
    ),
    array(
        "pattern" => "blog",
        "controller" => "home",
        "action" => "blog"
    ),
    array(
        "pattern" => "blog/:title/:id",
        "controller" => "home",
        "action" => "post"
    ),
    array(
        "pattern" => "contact",
        "controller" => "home",
        "action" => "contact"
    ),
    array(
        "pattern" => "privacy",
        "controller" => "home",
        "action" => "privacy"
    ),
    array(
        "pattern" => "termsofservice",
        "controller" => "home",
        "action" => "termsofservice"
    ),
    array(
        "pattern" => "resume-for-internship",
        "controller" => "resumes",
        "action" => "about"
    ),
    array(
        "pattern" => "placement-papers",
        "controller" => "placementpaper",
        "action" => "companies"
    ),
    array(
        "pattern" => "experience/:title/:id",
        "controller" => "placementpaper",
        "action" => "experience"
    ),
    array(
        "pattern" => "organization/:name/:id",
        "controller" => "organizations",
        "action" => "organization"
    ),
    array(
        "pattern" => "login",
        "controller" => "home",
        "action" => "login"
    ),
    array(
        "pattern" => "home",
        "controller" => "home",
        "action" => "index"
    ),
    array(
        "pattern" => "about",
        "controller" => "home",
        "action" => "about"
    ),
    array(
        "pattern" => "logout",
        "controller" => "home",
        "action" => "logout"
    ),
    array(
        "pattern" => "fonts/:id",
        "controller" => "files",
        "action" => "fonts"
    ),
    array(
        "pattern" => "thumbnails/:id",
        "controller" => "files",
        "action" => "thumbnails"
    ),
    array(
        "pattern" => "files/delete/:id",
        "controller" => "files",
        "action" => "delete"
    ),
    array(
        "pattern" => "files/undelete/:id",
        "controller" => "files",
        "action" => "undelete"
    ),
    array(
        "pattern" => "internship/:title/:id",
        "controller" => "home",
        "action" => "internship"
    ),
    array(
        "pattern" => ":title/:id",
<<<<<<< HEAD
        "controller" => "home",
        "action" => "opportunity"
=======
        "controller" => "opportunities",
        "action" => "view"
    ),
    array(
        "pattern" => "test-details/:title/:id",
        "controller" => "onlinetest",
        "action" => "test_details"
    ),
    array(
        "pattern" => "test/:title/:id",
        "controller" => "onlinetest",
        "action" => "test"
    ),
    array(
        "pattern" => "test-participated/:title/:id",
        "controller" => "onlinetest",
        "action" => "test_participated"
    ),
    array(
        "pattern" => "tests",
        "controller" => "onlinetest",
        "action" => "index"  
    ),
    array(
        "pattern" => "test",
        "controller" => "onlinetest",
        "action" => "index"  
    ),
    array(
        "pattern" => "result/:participant_id",
        "controller" => "onlinetest",
        "action" => "test_result"  
    ),
    array(
        "pattern" => "online-certification-exams",
        "controller" => "onlinetest",
        "action" => "certification"  
    ),
    array(
        "pattern" => "certificate/:certi_id",
        "controller" => "onlinetest",
        "action" => "test_certi"
>>>>>>> origin/secondary
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);