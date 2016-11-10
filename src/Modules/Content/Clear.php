<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Project;
use Tapestry\Step;

class Clear implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Clear constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $tmpPath = $project->currentWorkingDirectory . DIRECTORY_SEPARATOR . '.tmp';
        $output->writeln('[+] Clearing tmp folder ['. $tmpPath .']');

        if (file_exists($tmpPath)){
            $this->filesystem->remove($tmpPath);
        }
        $this->filesystem->mkdir($tmpPath);

        $output->writeln('[+] Clearing destination folder ['. $project->destinationDirectory .']');
        if (file_exists($project->destinationDirectory)){
            $this->filesystem->remove($project->destinationDirectory);
        }
        return true;
    }
}
