<?php

namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Cache;
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
        if ($project->get('cmd_options.clear') === true) {
            $output->writeln('[+] Clearing destination folder ['.$project->destinationDirectory.']');
            if (file_exists($project->destinationDirectory)) {
                $this->filesystem->remove($project->destinationDirectory);
            }

            $output->writeln('[+] Clearing cache');

            /** @var Cache $cache */
            $cache = $project->get('cache');
            $cache->reset();
        }

        return true;
    }
}
