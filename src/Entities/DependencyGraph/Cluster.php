<?php

namespace Tapestry\Entities\DependencyGraph;


/**
 * Class Cluster
 *
 * In order to avoid circular dependencies Clusters have been introduced that provide a
 * interface for creating virtual dependencies that will return isSame => false if any
 * one of their edges do so.
 */
class Cluster implements Node
{
    /**
     * @var Node[]
     */
    private $edges = [];

    /**
     * @var string
     */
    private $uid;

    /**
     * Cluster constructor.
     * @param string $uid
     * @param array|Node[] $edges
     */
    public function __construct(string $uid, array $edges = [])
    {
        $this->uid = $uid;
        $this->edges = $edges;
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
     * Add a node to this cluster.
     *
     * @param Node $node
     */
    public function addEdge(Node $node)
    {
        $this->edges[$node->getUid()] = $node;
    }

    /**
     * @return Node[]|array
     */
    public function getEdges(): array
    {
        return []; // This is a virtual node that will result in circular dependencies if it returns its edges.
    }

    /**
     * Compare a node (from cache) to see if it is valid.
     *
     * Useful for reducing the node graph to just those that have
     * been modified.
     *
     * Will return false if the node being compared is newer or different.
     *
     * Must be used against nodes of the same id, will throw an exception
     * if the id is different.
     *
     * @param Node $node
     * @return bool
     */
    public function isSame(Node $node): bool
    {
        return true;
        // TODO: Implement isSame() method.
    }
}