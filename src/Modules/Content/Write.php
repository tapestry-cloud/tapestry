<?php namespace Tapestry\Modules\Content;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Filesystem\FileCopier;
use Tapestry\Entities\Filesystem\FileWriter;
use Tapestry\Entities\Project;
use Tapestry\Step;

class Write implements Step
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
     * @param Filesystem $filesystem
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
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        /** @var FileCopier|FileWriter $file */
        foreach ($project['compiled']->all() as $file) {
            $file->__invoke($this->filesystem, $output);
        }

        return true;
    }
}
