<?php
use \Tapestry\Console\Application;

$tapestry = require_once __DIR__ . '/../src/bootstrap.php';

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();