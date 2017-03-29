<?php

use Tapestry\Console\Application;

if (isset($include)) {
    require_once $include.'/src/bootstrap.php';
} else {
    require_once __DIR__.'/../../src/bootstrap.php';
}

try {

    $tapestry = new Tapestry\Tapestry(
        new \Tapestry\Console\Input(
            $_SERVER['argv'],
            new \Tapestry\Console\DefaultInputDefinition()
        )
    );

    /** @var Application $cli */
    $cli = $tapestry[Application::class];
    $cli->run();

} catch (\Exception $e) {
    echo 'Uncaught Exception ' . get_class($e) . ' with message: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
    exit(1);
}