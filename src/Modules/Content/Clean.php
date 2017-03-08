<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class Clean implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Clear constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Process the Project at current.
     *
     * @param Project         $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        $tmpPath = $project->currentWorkingDirectory.DIRECTORY_SEPARATOR.'.tmp';
        $output->writeln('[+] Clearing tmp folder ['.$tmpPath.']');

        if (file_exists($tmpPath)) {
            $this->filesystem->remove($tmpPath);
        }

        return true;
    }
}
