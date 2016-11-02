<?php namespace Tapestry\Modules\Scripts;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\Project;
use Tapestry\Step;

class LoadContentTypes implements Step
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * LoadContentTypes constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
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
        if (! $contentTypes = $this->configuration->get('content_types', null)) {
            $output->writeln('[!] Your project\'s content types are miss-configured. Doing nothing and exiting.]');
        }

        foreach ($contentTypes as $name => $settings)
        {
            $project->set('content_types.'. $name, new ContentType($name, $settings));
        }

        return true;
    }
}
