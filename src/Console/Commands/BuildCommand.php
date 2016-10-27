<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Project;

class BuildCommand extends Command
{
    /**
     * Current Working Directory as set by user input --site-dir or getcwd() by default.
     * @var string
     */
    private $currentWorkingDirectory;
    /**
     * @var array
     */
    private $steps;

    /**
     * @var string
     */
    private $environment;

    /**
     * InitCommand constructor.
     * @param array $steps
     * @param string $currentWorkingDirectory
     * @param $environment
     */
    public function __construct(array $steps, $currentWorkingDirectory, $environment)
    {
        parent::__construct();
        $this->currentWorkingDirectory = $currentWorkingDirectory;
        $this->steps = $steps;
        $this->environment = $environment;
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
        if (!file_exists($this->currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> The site directory ['. $this->currentWorkingDirectory .'] does not exist. Doing nothing and exiting.');
            exit(1);
        }

        // Lets use full paths.
        if (! $this->currentWorkingDirectory = realpath($this->currentWorkingDirectory)) {
            $this->output->writeln('<error>[!]</error> Sorry there has been an error identifying the site directory. Doing nothing and exiting.');
            exit(1);
        }

        $project = new Project($this->steps, $this->currentWorkingDirectory, $this->environment);
        $project->setOutput($this->output);
        $project->compile();
    }
}