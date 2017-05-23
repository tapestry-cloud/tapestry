<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('src');

return new Sami($iterator, [
    'title'               => 'Tapestry API (for development)',
    'theme'               => 'default',
    'build_dir'           => __DIR__.'/build/docs',
    'cache_dir'           => __DIR__.'/build/cache',
    'include_parent_data' => false,
]);
