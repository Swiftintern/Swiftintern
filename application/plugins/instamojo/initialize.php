<?php

// initialize instamojo
include("instamojo.php");

$instamojo = new Instamojo('92b53ace475f2c661964d9671eabe9b5', 'f06eac17098ccb1d4bb50d1d1b4d01ed');

Framework\Registry::set("instamojo", $instamojo);