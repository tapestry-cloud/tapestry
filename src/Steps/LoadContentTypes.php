<?php

namespace Tapestry\Steps;

use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Symbol;
use Tapestry\Entities\Tree\Tree;
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

        /** @var Tree $tree */
        $tree = $project['ast'];

        $tree->add(new Leaf('content_type.default', new Symbol('content_type.default', Symbol::SYMBOL_CONTENT_TYPE, -1)), 'configuration');

        $contentTypeFactory = new ContentTypeCollection([
            new ContentType('default', [
                'path'      => '*',
                'permalink' => '*',
                'enabled'   => true,
            ]),
        ]);

        foreach ($contentTypes as $name => $settings) {
            $contentTypeFactory->add(new ContentType($name, $settings));

            $symbol = new Symbol('content_type.' . $name, Symbol::SYMBOL_CONTENT_TYPE, -1);
            $symbol->setHash(sha1(json_encode($settings)));
            $tree->add(new Leaf('content_type.' . $name, $symbol), 'configuration');
        }

        $project->set('content_types', $contentTypeFactory);

        return true;
    }
}
