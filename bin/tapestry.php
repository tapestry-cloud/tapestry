<?php

use \Tapestry\Console\Application;


if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
}else{
    require_once __DIR__.'/../src/bootstrap.php';
}
$tapestry = new \Tapestry\Tapestry();

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
