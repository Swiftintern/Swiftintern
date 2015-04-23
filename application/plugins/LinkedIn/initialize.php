<?php

// initialize linkedin
include("LinkedIn.php");

$linkedin = new LinkedIn(array(
    'api_key' => '78hui6bl8zcd0l',
    'api_secret' => 'z8ZNHwo9XViE0t0f',
    'callback_url' => 'http://swiftintern.com/home/linkedin'
));

Framework\Registry::set("linkedin", $linkedin);
