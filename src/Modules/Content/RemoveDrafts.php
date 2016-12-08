<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Entities\Configuration;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDrafts implements Step
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * RemoveDrafts constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
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
        if (boolval($this->configuration->get('publish_drafts', false)) === false) {
            /** @var File $file */
            foreach ($project->get('files')->all() as $file) {
                if (boolval($file->getData('draft', false)) === true) {
                    $project->removeFile($file);
                }
            }
            unset($file);
        }

        return true;
    }
}
