<?php

namespace Tapestry\Steps;

use Symfony\Component\Console\Output\OutputInterface;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Modules\Collectors\CollectorCollection;
use Tapestry\Step;
use Tapestry\Tapestry;

/**
 * Class LoadContentCollectors
 *
 * This Step looks up the configured collectors and fills the `collectors` object
 * for the given Project. This is required to have been invoked after
 * LoadContentTypes because it excludes their paths from `IsIgnoredMutator`.
 *
 * @package Tapestry\Steps
 */
class LoadContentCollectors implements Step
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var \League\Container\ContainerInterface
     */
    private $container;

    /**
     * LoadContentCollectors constructor.
     *
     * @param Tapestry $tapestry
     * @param Configuration $configuration
     */
    public function __construct(Tapestry $tapestry, Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->container = $tapestry->getContainer();
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function __invoke(Project $project, OutputInterface $output): Bool
    {
        $collection = new CollectorCollection();

        foreach ($this->configuration->get('content_collectors', []) as $name => $collectorConfig) {
            // Replace any %xxx% values with public Project properties.
            foreach ($collectorConfig as $key => $value) {
                if (!is_string($value)) { continue; }
                if (preg_match_all("/%(\w+)%/", $value, $matches) > 0) {
                    $collectorConfig[$key] = $project->{$matches[1][0]};
                }
            }

            // If mutatorCollection exist, create their classes.
            if (isset($collectorConfig['mutatorCollection'])) {
                foreach ($collectorConfig['mutatorCollection'] as &$mutator)
                {
                    $mutator = $this->container->get($mutator);
                }
                unset($mutator);
            }

            // If filterCollection exist, create their classes.
            if (isset($collectorConfig['filterCollection'])) {
                foreach ($collectorConfig['filterCollection'] as &$exclusion)
                {
                    $exclusion = $this->container->get($exclusion);
                }
                unset($exclusion);
            }

            $class = new \ReflectionClass($collectorConfig['collector']);
            $params = [];

            foreach ($class->getConstructor()->getParameters() as $parameter)
            {
                $params[$parameter->name] = $collectorConfig[$parameter->name];
            }

            $class = $class->newInstanceArgs($params);

            //$class = new $collectorConfig['collector']($collectorConfig[$constructorParameters[0]->name], $collectorConfig[$constructorParameters[1]->name], $collectorConfig[$constructorParameters[2]->name]);
            $n = 1;
        }

        $project['content_collectors'] = $collection;
        return false;
    }
}