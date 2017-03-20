<?php

use Tapestry\Console\Application;

if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
} else {
    require_once __DIR__.'/../src/bootstrap.php';
}

$tapestry = new Tapestry\Tapestry(
    new \Tapestry\Console\Input(
        $_SERVER['argv'],
        new \Tapestry\Console\DefaultInputDefinition()
    )
);

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
