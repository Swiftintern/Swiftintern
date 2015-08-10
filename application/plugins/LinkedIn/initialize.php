<?php

// initialize linkedin
include("LinkedIn.php");

$linkedin = new LinkedIn(array(
    'api_key' => 'api-key',
    'api_secret' => 'api-secret',
    'callback_url' => 'http://swiftintern.com/login'
));

Framework\Registry::set("linkedin", $linkedin);
