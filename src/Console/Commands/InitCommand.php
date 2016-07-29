<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    private $currentWorkingDirectory;

    /**
     * InitCommand constructor.
     * @param Filesystem $filesystem
     * @param string $currentWorkingDirectory
     */
    public function __construct(Filesystem $filesystem, $currentWorkingDirectory)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Scaffold a new project.')
            ->addArgument( 'name', InputArgument::OPTIONAL, 'Where should we initialise this project?' );
    }

    protected function fire()
    {
        if ($name = $this->input->getArgument('name')) {
            $this->currentWorkingDirectory .= ( (substr($this->currentWorkingDirectory, -1, 1) === DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR) . $name;
        }



        $this->info('Site initiated successfully in ['. $this->currentWorkingDirectory .']');
        return 0;
    }
}