<?php

namespace Tapestry\Modules\ContentTypes;

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
        if (!$contentTypes = $this->configuration->get('content_types', null)) {
            $output->writeln('[!] Your project\'s content types are miss-configured. Doing nothing and exiting.]');
        }

        $contentTypeFactory = new ContentTypeFactory([
            new ContentType('default', [
                'path'      => '*',
                'permalink' => '*',
                'enabled'   => true,
            ]),
        ]);

        foreach ($contentTypes as $name => $settings) {
            $contentTypeFactory->add(new ContentType($name, $settings));
        }

        $project->set('content_types', $contentTypeFactory);

        return true;
    }
}
