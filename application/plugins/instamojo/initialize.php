<?php

// initialize instamojo
include("instamojo.php");

$instamojo = new Instamojo('api-key', 'auth-token');

Framework\Registry::set("instamojo", $instamojo);