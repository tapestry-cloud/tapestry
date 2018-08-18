<?php

namespace Tapestry\Entities;

use Tapestry\ArrayContainer;
use Tapestry\Entities\DependencyGraph\Node;
use Tapestry\Exceptions\GraphException;
use Tapestry\Entities\DependencyGraph\Graph;
use Tapestry\Modules\Generators\ContentGeneratorFactory;
use Tapestry\Modules\Generators\GeneratorInterface;
use Tapestry\Modules\Source\SourceInterface;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Collections\FlatCollection;

class Project extends ArrayContainer
{
    /**
     * @var string
     */
    public $sourceDirectory;

    /**
     * @var string
     */
    public $destinationDirectory;

    /**
     * @var string
     */
    public $currentWorkingDirectory;

    /**
     * @var string
     */
    public $environment;

    /**
     * Project constructor.
     *
     * @param string $cwd
     * @param string $dist
     * @param string $environment
     */
    public function __construct(string $cwd, string $dist, string $environment)
    {
        $this->sourceDirectory = $cwd.DIRECTORY_SEPARATOR.'source';
        $this->destinationDirectory = $dist;

        $this->currentWorkingDirectory = $cwd;
        $this->environment = $environment;

        parent::__construct(
            [
                'files' => new FlatCollection(), // @todo deprecate files and compiled in preference of graph. If a source is changed since last execution it gets re-loaded from disk anyway!
                'graph' => new Graph(),
            ]
        );
    }

    /**
     * @param SourceInterface|FileGenerator $file
     * @throws \Exception
     * @deprecated
     */
    public function addFile(SourceInterface $file)
    {
        throw new \Exception('addFile method on Project is deprecated, use addSource instead.');
    }

    /**
     * @param string $parent id of parent node.
     * @param Node $source all source being added to Project must implement Node interface.
     * @throws GraphException
     */
    public function addSource(string $parent, Node $source)
    {
        $this->getGraph()->addEdge($parent, $source);
    }

    /**
     * @param string $key
     *
     * @return void
     * @throws \Exception
     * @deprecated
     */
    public function getFile($key)
    {
        throw new \Exception('getFile method on Project is deprecated, use getSource instead.');
    }

    /**
     * @param string $uid
     * @return Node|SourceInterface
     * @throws GraphException
     */
    public function getSource(string $uid): Node
    {
        return $this->getGraph()->getEdge($uid);
    }

    /**
     * @return SourceInterface[]|FlatCollection
     * @throws GraphException
     */
    public function allSources(): FlatCollection
    {
        return new FlatCollection($this->getGraph()->getTable());
    }

    /**
     * @param string $name
     * @param SourceInterface   $file
     *
     * @return GeneratorInterface
     */
    public function getContentGenerator(string $name, SourceInterface $file): GeneratorInterface
    {
        /** @var ContentGeneratorFactory $factory */
        $factory = $this->get('content_generators');
        return $factory->get($name, $file);
    }

    /**
     * @param string $name
     *
     * @return \Tapestry\Modules\ContentTypes\ContentType
     */
    public function getContentType($name)
    {
        return $this->get('content_types.'.$name);
    }

    /**
     * @return Graph
     * @throws GraphException
     */
    public function getGraph(): Graph
    {
        if (! $this->has('graph')) {
            throw new GraphException('Graph is not initiated');
        }

        return $this->get('graph');
    }
}
