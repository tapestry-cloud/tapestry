<?php

use Tapestry\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
} else {
    require_once __DIR__.'/../src/bootstrap.php';
}

$definitions = new \Tapestry\Console\DefaultInputDefinition();
$tapestry = new Tapestry\Tapestry(new ArgvInput(null, $definitions));

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
