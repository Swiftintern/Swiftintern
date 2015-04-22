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
        "controller" => "home",
        "action" => "opportunity"
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);