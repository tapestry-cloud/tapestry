<?php

date_default_timezone_set('UTC');

$json = json_encode([
    'hash' => exec('git rev-parse --short --verify HEAD'),
    'date' => date('c'),
]);

file_put_contents(__DIR__.'/../src/build.json', $json);
