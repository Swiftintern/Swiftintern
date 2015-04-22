<?php

// define routes

$routes = array(
    array(
        "pattern" => "student/register",
        "controller" => "students",
        "action" => "register"
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
        "pattern" => "login",
        "controller" => "users",
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
        "controller" => "users",
        "action" => "logout"
    ),
    array(
        "pattern" => "search",
        "controller" => "users",
        "action" => "search"
    ),
    array(
        "pattern" => "profile",
        "controller" => "users",
        "action" => "profile"
    ),
    array(
        "pattern" => "settings",
        "controller" => "users",
        "action" => "settings"
    ),
    array(
        "pattern" => "unfriend/:id",
        "controller" => "users",
        "action" => "friend"
    ),
    array(
        "pattern" => "friend/:id",
        "controller" => "users",
        "action" => "friend"
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
        "pattern" => "users/edit/:id",
        "controller" => "users",
        "action" => "edit"
    ),
    array(
        "pattern" => "users/delete/:id",
        "controller" => "users",
        "action" => "delete"
    ),
    array(
        "pattern" => "users/undelete/:id",
        "controller" => "users",
        "action" => "undelete"
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
        "pattern" => ":title/:id",
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
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);