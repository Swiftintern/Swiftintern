<?php

// initialize seo
include("seo.php");

$seo = new SEO(array(
    "title" => "Internship Training | Internship India 2015",
    "keywords" => "Internship india, Internship in Mumbai, The Internship, Internship training, Internship in bangalore, Summer internship india" ,
    "description" => "Apply to thousands of Internships in Mumbai, bangalore, delhi, chennai, hyderabad for B.Tech, MBA, etc Students",
    "author" => "https://plus.google.com/107837531266258418226",
    "robots" => "INDEX,FOLLOW",
    "photo" => CDN . "images/logo.png"
));

Framework\Registry::set("seo", $seo);