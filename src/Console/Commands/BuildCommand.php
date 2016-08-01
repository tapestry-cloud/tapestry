<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class BuildCommand extends Command
{
    /**
     * Current Working Directory as set by user input --site-dir or getcwd() by default.
     * @var string
     */
    private $currentWorkingDirectory;

    /**
     * InitCommand constructor.
     * @param string $currentWorkingDirectory
     * @param $environment
     */
    public function __construct($currentWorkingDirectory, $environment)
    {
        parent::__construct();
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build Project.');
    }

    protected function fire()
    {
        dd($this->currentWorkingDirectory);
    }
}