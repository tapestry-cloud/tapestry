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
        $this->setName('init')
            ->setDescription('Scaffold a new project.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Where should we initialise this project?');
    }

    protected function fire()
    {
        $currentWorkingDirectory = $this->input->getOption('site-dir');

        // If the current working directory does not exist, do nothing and exit. This is often due to a borked --site-dir
        if (!$this->filesystem->exists($currentWorkingDirectory)) {
            $this->error('The project directory [' . $currentWorkingDirectory . '] does not exist. Doing nothing and exiting.');
            return 1;
        }

        if ($name = $this->input->getArgument('name')) {
            $currentWorkingDirectory .= ((substr($currentWorkingDirectory, -1, 1) === DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR) . $name;
        }

        if ($this->filesystem->exists($currentWorkingDirectory) && $this->finder->in($currentWorkingDirectory)->count() > 0) {
            $this->error('The project directory [' . $currentWorkingDirectory . '] is not empty. Doing nothing and exiting.');
            return 1;
        }

        if (!$this->filesystem->exists($currentWorkingDirectory)) {
            $this->filesystem->mkdir($currentWorkingDirectory);

            if (!$this->filesystem->exists($currentWorkingDirectory)) {
                $this->error('The project directory [' . $currentWorkingDirectory . '] could not be created.');
                return 1;
            }
        }

        $sourcePath = __DIR__ . '/../../Scaffold';

        /** @var SplFileInfo $file */
        foreach($this->finder->in($sourcePath) as $file){
            $fromPath = $sourcePath . DIRECTORY_SEPARATOR . $file->getRelativePathname();
            $toPath = $currentWorkingDirectory . DIRECTORY_SEPARATOR . $file->getRelativePathname();

            if ($file->isDir()){
                $this->output->writeln('[*] Creating Directory ['. $toPath .']');
                $this->filesystem->mkdir($toPath);
            }else{
                $this->output->writeln('[*] Copying ['. $fromPath .'] to ['. $toPath .']');
                $this->filesystem->copy($fromPath, $toPath);
            }
        }

        $this->info('Project initiated successfully in [' . $currentWorkingDirectory . ']');
        return 0;
    }
}