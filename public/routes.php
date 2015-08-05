<?php

// define routes

$routes = array(
    array(
        "pattern" => "student/register",
        "controller" => "students",
        "action" => "register"
    ),
    array(
        "pattern" => "student",
        "controller" => "students",
        "action" => "index"
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
        "pattern" => "test/:title/:id",
        "controller" => "onlinetest",
        "action" => "test"
    ),
    array(
        "pattern" => "support",
        "controller" => "home",
        "action" => "support"
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
        "pattern" => "partners",
        "controller" => "home",
        "action" => "partners"
    ),
    array(
        "pattern" => "termsofservice",
        "controller" => "home",
        "action" => "termsofservice"
    ),
    array(
        "pattern" => "resume-for-internship",
        "controller" => "resumes",
        "action" => "index"
    ),
    array(
        "pattern" => "resume",
        "controller" => "resumes",
        "action" => "index"
    ),
    array(
        "pattern" => "placement-papers",
        "controller" => "organizations",
        "action" => "placementpapers"
    ),
    array(
        "pattern" => "experience/:title/:id",
        "controller" => "organizations",
        "action" => "experience"
    ),
    array(
        "pattern" => "organization/:name/:id",
        "controller" => "organizations",
        "action" => "organization"
    ),
    array(
        "pattern" => "login",
        "controller" => "users",
        "action" => "login"
    ),
    array(
        "pattern" => "studentPartner",
        "controller" => "internship",
        "action" => "studentPartner"
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
        "pattern" => "thumbnails/:id",
        "controller" => "home",
        "action" => "thumbnails"
    ),
    array(
        "pattern" => "internship/edit/:id",
        "controller" => "internship",
        "action" => "edit"
    ),
    array(
        "pattern" => "internship/applicants/:id",
        "controller" => "internship",
        "action" => "applicants"
    ),
    array(
        "pattern" => "internship/addQuestions/:id",
        "controller" => "internship",
        "action" => "addQuestions"
    )
    ,
    array(
        "pattern" => "internship/editQuestions/:id",
        "controller" => "internship",
        "action" => "editQuestions"
    ),
    array(
        "pattern" => "internship/:title/:id",
        "controller" => "internship",
        "action" => "details"
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);