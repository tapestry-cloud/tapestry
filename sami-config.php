<?php

use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Sami;
use Sami\Version\GitVersionCollection;


$dir = realpath(__DIR__ .'\src');

$versions = GitVersionCollection::create($dir)
    ->addFromTags('1.0.*')
    ->add('2.0.0-dev', '2.0.0 dev-branch')
    ->add('master', 'master branch')
;

return new Sami($dir, [
    'versions' => $versions,
    'title' => 'Tapestry API',
    'build_dir' => __DIR__.'/build/tapestry/%version%',
    'cache_dir' => __DIR__.'/cache/tapestry/%version%',
    'remote_repository' => new GitHubRemoteRepository('tapestry-cloud/tapestry', dirname($dir)),
    'default_opened_level' => 2
]);