<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
     * @var string
     */
    private $releaseApi = 'https://api.github.com/repos/carbontwelve/tapestry/releases/latest';

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
            ->setDescription('Update your installed version of Tapestry.')
            ->setDefinition([
                new InputOption('rollback', 'r', InputOption::VALUE_NONE, 'Revert to an older installation of tapestry'),
                new InputOption('clean-backups', null, InputOption::VALUE_NONE, 'Delete old backups during an update. This makes the current version of tapestry the only backup available after the update'),
            ]);
    }

    protected function fire()
    {
        $localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        if (pathinfo($localFilename, PATHINFO_EXTENSION) !== 'phar'){
            $this->output->writeln('[!] Self-Update only works on phar archives.');
            exit(1);
        }

        $tempFilename = dirname($localFilename) . '/' . basename($localFilename, '.phar').'-temp.phar';
        dd($tempFilename);
        return 0;
    }
}