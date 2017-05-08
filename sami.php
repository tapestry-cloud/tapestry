<?php
use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Scaffold')
    ->in($dir = __DIR__ . '/src');

// generate documentation for all v2.0.* tags, the 2.0 branch, and the master one
$versions = GitVersionCollection::create($dir)
    //->addFromTags('1.0.*')
    ->add('development', 'development branch')
    ->add('master', 'master branch');

return new Sami($iterator, array(
    //'theme' => 'symfony',
    'versions' => $versions,
    'title' => 'Tapestry API',
    'build_dir' => __DIR__ . '/build/docs/%version%',
    'cache_dir' => __DIR__ . '/build/cache/%version%',
    'remote_repository' => new GitHubRemoteRepository('symfony/symfony', dirname($dir)),
    'default_opened_level' => 2,
));