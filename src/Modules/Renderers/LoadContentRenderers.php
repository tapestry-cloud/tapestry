<?php namespace Tapestry\Modules\Renderers;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Step;
use Tapestry\Tapestry;

class LoadContentRenderers implements Step
{

    /**
     * @var \League\Container\ContainerInterface
     */
    private $container;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * LoadRenderers constructor.
     * @param Tapestry $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->container = $tapestry->getContainer();
        $this->configuration = $configuration;
    }

    /**
     * @param Project $project
     * @param OutputInterface $output
     * @return boolean
     */
    public function __invoke(Project $project, OutputInterface $output)
    {

        if (! $contentRenderers = $this->configuration->get('content_renderers', null)) {
            $output->writeln('[!] Your project\'s content renderers are miss-configured. Doing nothing and exiting.]');
        }

        $contentRendererFactory = new ContentRendererFactory();

        foreach($contentRenderers as $renderer) {
            $contentRendererFactory->add($this->container->get($renderer));
        }

        $project->set('content_renderers', $contentRendererFactory);

        $n =1;
        return true;

    }
}
