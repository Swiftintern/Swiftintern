<?php
require_once 'request.php';
require_once 'response.php';
require_once 'document.php';
require_once 'webbot.php';

// set unlimited execution time
set_time_limit(0);

// set the default timeout to 1 seconds
\WebBot\WebBot::$conf_default_timeout = 1;

// set delay between consecutive fetches
\WebBot\WebBot::$conf_delay_between_fetches = 25;

// do not use HTTPS protocol
\WebBot\WebBot::$conf_force_https = false;

// don't include document field raw values
\WebBot\WebBot::$conf_include_document_field_raw_values = false;

// set the directory for storing information
\WebBot\WebBot::$conf_store_dir = APP_PATH."/logs/";
?>