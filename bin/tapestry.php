<?php

use Tapestry\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
} else {
    require_once __DIR__.'/../src/bootstrap.php';
}

//
// Because Tapestry only needs to know about options passed via argv we filter everything else out.
//
$argvInput = new ArgvInput(array_filter($_SERVER['argv'], function ($value) {
    return strpos($value, '-') !== false;
}), new \Tapestry\Console\DefaultInputDefinition());
$tapestry = new Tapestry\Tapestry($argvInput);

/** @var Application $cli */
$cli = $tapestry[Application::class];
$cli->run();
