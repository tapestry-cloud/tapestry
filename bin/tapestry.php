<?php

use Symfony\Component\Console\Input\ArgvInput;
use Tapestry\Console\Application;

if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
} else {
    require_once __DIR__.'/../src/bootstrap.php';
}
$tapestry = new Tapestry\Tapestry(new ArgvInput());

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
