<?php
$client_id = '876898912833-dquv6pith1jn0rre7gahrgdol26u3qbt.apps.googleusercontent.com';
$client_secret = 'jG59-0YXVKnGZAaYwE_LYFU1';
$redirect_uri = 'http://localhost/swiftintern/students/register';

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes('profile');
$client->addScope('email');

Framework\Registry::set("gClient", $client);
