<?php

namespace Tapestry\Console\Commands;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class MakeCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * InitCommand constructor.
     *
     * @param Filesystem $filesystem
     * @param Finder     $finder
     */
    public function __construct(Filesystem $filesystem, Finder $finder)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->finder = $finder;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('make')
            ->setDescription('Workspace scaffold system.')
            ->setHelp('This command allows you to run workspace scaffolds as defined by your site kernel.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Which scaffold should we use?');
    }

    protected function fire()
    {
        return 0;
    }
}
