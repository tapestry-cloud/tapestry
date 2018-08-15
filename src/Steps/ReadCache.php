<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Tapestry;
use Tapestry\Entities\Cache;
use Tapestry\Entities\Project;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReadCache.
 *
 * This Step opens the cache file if found for the configured environment and loads it into the Project Container.
 */
class ReadCache implements Step
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * ReadCache constructor.
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Invoke a new instance of the Cache system, load it and then inject it into the Project Container.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Tapestry\Exceptions\InvalidVersionException
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $cache = new Cache($project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.'.$project->environment.'_cache',
            $this->createInvalidationHash($project));
        $cache->load();
        $project->set('cache', $cache);

        return true;
    }

    /**
     * Find files non recursively within the src folder and create a hash of their content salted with the applications
     * version number. This ensures that the cache is invalidated upon either the base directory changing (including
     * config.php and kernel.php, or files such as Gulp, Grunt config;) as well as if the user updates their version of
     * the application.
     *
     * @param Project $project
     * @return string
     */
    private function createInvalidationHash(Project $project)
    {
        $files = $this->finder->files()->in($project->currentWorkingDirectory)->depth('== 0');
        $hash = [];

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            array_push($hash, sha1_file($file->getPathname()));
        }

        array_push($hash, sha1(Tapestry::VERSION));

        return sha1(implode('.', $hash));
    }
}
