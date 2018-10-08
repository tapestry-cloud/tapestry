<?php

namespace Tapestry\Modules\ContentTypes;

use Tapestry\Entities\DependencyGraph\Node;
use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;
use Tapestry\Entities\DependencyGraph\SimpleNode;

class ContentTypeCollection
{
    /**
     * Registered item stack.
     *
     * @var array|ContentType[]
     */
    private $items = [];

    /**
     * Registered item lookup table.
     *
     * @var array
     */
    private $pathLookupTable = [];

    /**
     * Registered items name lookup table.
     *
     * @var array
     */
    private $nameLookupTable = [];

    /**
     * @var Project
     */
    private $project;

    /**
     * ContentTypeCollection constructor.
     *
     * @param array|ContentType[] $items
     * @param Project $project
     * @throws \Exception
     */
    public function __construct(array $items = [], Project $project)
    {
        $this->project = $project;

        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add a ContentType to the registry.
     *
     * @param ContentType $contentType
     * @param bool $overWrite should adding overwrite existing; if false an exception will be thrown if a matching collection already found
     *
     * @throws \Exception
     */
    public function add(ContentType $contentType, bool $overWrite = false)
    {
        if (! $overWrite && $this->has($contentType->getPath())) {
            throw new \Exception('The collection ['.$this->pathLookupTable[$contentType->getPath()].'] already collects for the path ['.$contentType->getPath().']');
        }
        $uid = sha1(md5(get_class($contentType)).'_'.sha1($contentType->getName().'-'.$contentType->getPath()));
        $this->items[$uid] = $contentType;
        $this->pathLookupTable[$contentType->getPath()] = $uid;
        $this->nameLookupTable[$contentType->getName()] = $uid;

        $templateFilePath = $this->project->sourceDirectory.DIRECTORY_SEPARATOR.$contentType->getTemplate().'.phtml';

        // I have added the hash of the content types template file to ensure that the
        // content type is invalid if its template changes.
        if ($contentType->getName() !== 'default' && file_exists($templateFilePath)) {
            $hash = sha1($uid.'.'.sha1_file($templateFilePath));
        } else {
            $hash = $uid;
        }

        $graph = $this->project->getGraph();

        $graph->addEdge('configuration', new SimpleNode('content_type.'.$contentType->getName(), $hash));
        foreach($contentType->getTaxonomies() as $taxonomy)
        {
            // @todo add taxonomy as a node?
        }
    }

    /**
     * Bucket a SourceFile into one of the ContentTypes in this Collection.
     *
     * @param SourceInterface|Node $source
     * @return ContentType
     * @throws \Exception
     */
    public function bucketSource(SourceInterface $source): ContentType
    {
        if (! $contentType = $this->find($source->getRelativePath())) {
            $contentType = $this->get('*');
        } else {
            $contentType = $this->get($contentType);
        }

        $this->project->getGraph()->addEdge('content_type.'.$contentType->getName(), $source);
        $contentType->addSource($source);

        return $contentType;
    }

    /**
     * Return true if the registry contains an absolute path.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path): bool
    {
        return isset($this->pathLookupTable[$path]);
    }

    /**
     * Return all ContentTypes registered with this factory.
     *
     * @return array|\Tapestry\Entities\ContentType[]
     */
    public function all(): array
    {
        return array_values($this->items);
    }

    /**
     * Returns the absolute path from an input, so that you may then get the ContentType that deals with it.
     *
     * @param string $path
     *
     * @return null|string
     */
    public function find(string $path)
    {
        foreach (array_keys($this->pathLookupTable) as $key) {
            if (starts_with($path, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get ContentType by its supported path. If $path is '*' then if a default ContentType has been set it will be
     * returned.
     *
     * @param string $path
     *
     * @throws \Exception
     *
     * @return ContentType
     */
    public function get($path): ContentType
    {
        if (! $this->has($path) && ! $this->has('*')) {
            throw new \Exception('There is no collection that collects for the path ['.$path.']');
        }

        if (! $this->has($path) && $this->has('*')) {
            return $this->items[$this->pathLookupTable['*']];
        }

        return $this->items[$this->pathLookupTable[$path]];
    }

    /**
     * ArrayAccess method for use by ArrayContainer for dot notation key retrieval.
     *
     * @param $key
     *
     * @return mixed|null|ContentType
     */
    public function arrayAccessByKey($key)
    {
        if (! isset($this->nameLookupTable[$key])) {
            return null;
        }

        if (isset($this->items[$this->nameLookupTable[$key]])) {
            return $this->items[$this->nameLookupTable[$key]];
        }

        return null;
    }
}
