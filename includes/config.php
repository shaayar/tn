<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','travelnest');
define('SITE_NAME','TravelNest');
define('TAX_RATE',0.12);
define('PER_PAGE',9);

if(session_status()===PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors',1);
