<?php

include 'Chart.php';

$chart = new Chart(array(
    "Jan" => 110,
    "Feb" => 130,
    "Mar" => 215,
    "Apr" => 81,
    "May" => 310,
    "Jun" => 110,
    "Jul" => 190,
    "Aug" => 175,
    "Sep" => 390,
    "Oct" => 286,
    "Nov" => 150,
    "Dec" => 196
));

$chart->drawBar(800, 400);
