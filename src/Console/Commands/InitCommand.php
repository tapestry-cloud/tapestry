<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class InitCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Current Working Directory as set by user input --site-dir or getcwd() by default.
     * @var string
     */
    private $currentWorkingDirectory;
    /**
     * @var Finder
     */
    private $finder;

    /**
     * InitCommand constructor.
     * @param Filesystem $filesystem
     * @param Finder $finder
     * @param string $currentWorkingDirectory
     */
    public function __construct(Filesystem $filesystem, Finder $finder, $currentWorkingDirectory)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Scaffold a new project.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Where should we initialise this project?');
    }

    protected function fire()
    {
        // If the current working directory does not exist, do nothing and exit. This is often due to a borked --site-dir
        if (!$this->filesystem->exists($this->currentWorkingDirectory)) {
            $this->error('The project directory [' . $this->currentWorkingDirectory . '] does not exist. Doing nothing and exiting.');
            return 1;
        }

        if ($name = $this->input->getArgument('name')) {
            $this->currentWorkingDirectory .= ((substr($this->currentWorkingDirectory, -1, 1) === DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR) . $name;
        }

        if ($this->filesystem->exists($this->currentWorkingDirectory) && $this->finder->in($this->currentWorkingDirectory)->count() > 0) {
            $this->error('The project directory [' . $this->currentWorkingDirectory . '] is not empty. Doing nothing and exiting.');
            return 1;
        }

        if (!$this->filesystem->exists($this->currentWorkingDirectory)) {
            $this->filesystem->mkdir($this->currentWorkingDirectory);

            if (!$this->filesystem->exists($this->currentWorkingDirectory)) {
                $this->error('The project directory [' . $this->currentWorkingDirectory . '] could not be created.');
                return 1;
            }
        }

        $sourcePath = __DIR__ . '/../../Scaffold';

        /** @var SplFileInfo $file */
        foreach($this->finder->in($sourcePath) as $file){
            $fromPath = $sourcePath . DIRECTORY_SEPARATOR . $file->getRelativePathname();
            $toPath = $this->currentWorkingDirectory . DIRECTORY_SEPARATOR . $file->getRelativePathname();

            if ($file->isDir()){
                $this->output->writeln('[*] Creating Directory ['. $toPath .']');
                $this->filesystem->mkdir($toPath);
            }else{
                $this->output->writeln('[*] Copying ['. $fromPath .'] to ['. $toPath .']');
                $this->filesystem->copy($fromPath, $toPath);
            }
        }

        $this->info('Project initiated successfully in [' . $this->currentWorkingDirectory . ']');
        return 0;
    }
}