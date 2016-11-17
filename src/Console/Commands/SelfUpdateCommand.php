<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SelfUpdateCommand extends Command
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
     * @param Filesystem $filesystem
     * @param Finder $finder
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
        $this->setName('self-update')
            ->setDescription('Update your installed version of Tapestry.');
    }

    protected function fire()
    {
        return 0;
    }
}