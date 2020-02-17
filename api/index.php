<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__DIR__));   //  /var/www/

require ROOT.'/vendor/autoload.php';

require ROOT.'/config/helpers.php';
require ROOT.'/config/db.php';
require ROOT.'/config/router.php';