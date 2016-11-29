<?php

use \Tapestry\Console\Application;

require_once __DIR__.'/../src/bootstrap.php';
$tapestry = new \Tapestry\Tapestry();

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
