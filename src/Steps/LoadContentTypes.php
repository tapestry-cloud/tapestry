<?php

namespace Tapestry\Steps;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentType;
use Tapestry\Entities\Configuration;
use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Modules\ContentTypes\ContentTypeCollection;

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
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Exception
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        if (! $contentTypes = $this->configuration->get('content_types', null)) {
            $output->writeln('[!] Your project\'s content types are miss-configured. Doing nothing and exiting.]');
        }

        $contentTypeFactory = new ContentTypeCollection([
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
