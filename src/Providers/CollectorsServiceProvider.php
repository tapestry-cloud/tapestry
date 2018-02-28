<?php

namespace Tapestry\Providers;


use League\Container\ServiceProvider\AbstractServiceProvider;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Modules\Collectors\Exclusions\DraftsExclusion;
use Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator;
use Tapestry\Modules\Collectors\Mutators\IsScheduledMutator;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;
use Tapestry\Tapestry;

class CollectorsServiceProvider extends AbstractServiceProvider
{

    /**
     * @var array
     */
    protected $provides = [
        IsScheduledMutator::class,
        IsIgnoredMutator::class,
        DraftsExclusion::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->registerIsScheduledMutatorFactory();
        $this->registerIsIgnoredMutatorFactory();
        $this->registerDraftsExclusionFactory();
    }

    private function registerDraftsExclusionFactory()
    {
        $container = $this->getContainer();
        $container->add(DraftsExclusion::class, function () use ($container) {
            /** @var Configuration $configuration */
            $configuration = $container->get(Configuration::class);

            $publishDrafts = boolval($configuration->get('publish_drafts', false));

            return new DraftsExclusion($publishDrafts);
        });
    }

    private function registerIsIgnoredMutatorFactory()
    {
        $container = $this->getContainer();
        $container->add(IsIgnoredMutator::class, function () use ($container) {
            /** @var Project::class $project */
            $project = $container->get(Project::class);

            /** @var Configuration $configuration */
            $configuration = $container->get(Configuration::class);

            /** @var ContentTypeFactory $contentTypes */
            $contentTypes = $project->get('content_types');

            $exclusions = [];
            foreach ($contentTypes->all() as $contentType) {
                $path = $contentType->getPath();
                if ($path !== '*' && ! isset($this->dontIgnorePaths[$contentType->getPath()])) {
                    $exclusions[] = $contentType->getPath();
                }
            }
            unset($contentType);

            return new IsIgnoredMutator(array_merge($configuration->get('ignore', []), ['_views', '_templates']), $exclusions);
        });
    }

    private function registerIsScheduledMutatorFactory()
    {
        $container = $this->getContainer();
        $container->add(IsScheduledMutator::class, function () use ($container) {
            /** @var Tapestry $tapestry */
            $tapestry = $container->get(Tapestry::class);

            /** @var Configuration $configuration */
            $configuration = $container->get(Configuration::class);

            $publishDrafts = boolval($configuration->get('publish_drafts', false));

            $autoPublish = (isset($tapestry['cmd_options']['auto-publish']) ? boolval($tapestry['cmd_options']['auto-publish']) : false);

            return new IsScheduledMutator($publishDrafts, $autoPublish);
        });
    }

}