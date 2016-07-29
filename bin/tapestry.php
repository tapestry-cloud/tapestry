<?php

if (!defined('TAPESTRY_START')) {
    define('TAPESTRY_START', microtime(true));
}

if (version_compare(phpversion(), '5.4', '<')) {
    die('You must use PHP >= 5.4 in order to use Couscous. Please upgrade your PHP version.');
}
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

// Phar includes
if (isset($include)) {
    require_once $include . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} else{
    echo "Please run composer install." . PHP_EOL;
    exit(1);
}

