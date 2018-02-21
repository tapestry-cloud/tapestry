<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Sami\RemoteRepository\GitHubRemoteRepository;

$dir = realpath(__DIR__.'\src');

$versions = GitVersionCollection::create($dir)
    ->addFromTags('1.0.*')
    ->add('2.0.0-dev', '2.0.0 dev-branch')
    ->add('master', 'master branch');

return new Sami($dir, [
    'versions' => $versions,
    'title' => 'Tapestry API',
    'build_dir' => __DIR__.'/build/docs/%version%',
    'cache_dir' => __DIR__.'/build/cache/%version%',
    'remote_repository' => new GitHubRemoteRepository('tapestry-cloud/tapestry', dirname($dir)),
    'default_opened_level' => 2,
]);
