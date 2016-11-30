<?php

use Phine\Phar\Builder;
use Phine\Phar\Stub;
use Symfony\Component\Finder\Finder;

require_once __DIR__.'/../vendor/autoload.php';

$baseDir = realpath(__DIR__.'/..');

//
// Delete existing phar if exists
//
if (file_exists(__DIR__.'/tapestry.phar')) {
    unlink(__DIR__.'/tapestry.phar');
}

//
// Create a new Phar in the same directory
//
$builder = Builder::create(__DIR__.'/tapestry.phar');

//
// Add Tapestry src to Phar
//
$builder->buildFromIterator(
    Finder::create()
        ->files()
        ->name('*.php')
        ->exclude(['Scaffold'])
        ->in($baseDir.'/src')
        ->getIterator(),
    $baseDir
);

//
// Add Tapestry Scaffold to Phar
//
$builder->buildFromIterator(
    Finder::create()
        ->files()
        ->in($baseDir.'/src/Scaffold')
        ->getIterator(),
    $baseDir
);

//
// Add Vendor dependencies to Phar
//
$builder->buildFromIterator(
    Finder::create()
        ->files()
        ->name('*.php')
        ->name('*.pem*')
        ->exclude(['Tests', 'tests', 'phpunit'])
        ->in($baseDir.'/vendor')
        ->getIterator(),
    $baseDir
);

$builder->addFile("$baseDir/LICENSE", 'LICENSE');

$source = file_get_contents($baseDir.'/bin/tapestry.php');
$source = str_replace('<?php', '', $source);

$builder->setStub(
    Stub::create()
        ->mapPhar('tapestry.phar')
        ->addSource($source)
        ->getStub()
);

chmod(__DIR__.'/tapestry.phar', 0755);
file_put_contents(__DIR__.'/tapestry.version', sha1_file(__DIR__.'/tapestry.phar'));
