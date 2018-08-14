<?php

namespace Tapestry\Entities\DependencyGraph;
use Tapestry\Exceptions\GraphException;

/**
 * Class SimpleNode
 *
 * This class exists to enable adding special files such as the configuration and kernel to the
 * dependency graph with the configuration being the root node and the kernel being its only
 * dependant. All other nodes will be siblings of the kernel node.
 */
class SimpleNode implements Node
{
    /**
     * A Unique Identifier for this Node.
     *
     * @var string
     */
    private $uid;

    /**
     * A SHA1 hash of the file contents that this Node references.
     *
     * @var string
     */
    private $hash;

    /**
     * The source files that depend upon this source.
     *
     * @var array Node[]
     */
    protected $edges = [];

    /**
     * SimpleNode constructor.
     * @param string $uid
     * @param string $hash
     */
    public function __construct(string $uid, string $hash)
    {
        $this->uid = $uid;
        $this->hash = $hash;
    }

    /**
     * Get this nodes uid.
     *
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Add a source that depends upon this source.
     *
     * @param Node $node
     */
    public function addEdge(Node $node)
    {
        $this->edges[$node->getUid()] = $node;
    }

    /**
     * Return a list of source objects that depend upon this one.
     *
     * @return array
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param SimpleNode|Node $node
     * @return bool
     * @throws GraphException
     */
    public function isSame(Node $node): bool
    {
        if ($node->getUid() !== $this->getUid()) {
            throw new GraphException('Node being compared must have the same identifier.');
        }

        if ($node->getHash() !== $this->getHash()) {
            return false;
        }

        return true;
    }
}