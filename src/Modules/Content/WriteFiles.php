<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Entities\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Filesystem\FilesystemInterface;

class WriteFiles implements Step
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Write constructor.
     *
     * @param Filesystem    $filesystem
     * @param Configuration $configuration
     */
    public function __construct(Filesystem $filesystem, Configuration $configuration)
    {
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
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
        if (isset($project['cmd_options']['no-write']) && $project['cmd_options']['no-write'] === false) {
            return true;
        }

        /** @var FilesystemInterface $file */
        foreach ($project['compiled']->all() as $file) {
            $file->__invoke($this->filesystem, $output);
        }

        return true;
    }
}
