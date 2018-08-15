<?php

namespace Tapestry\Entities\DependencyGraph;

interface Node
{
    /**
     * Get this nodes uid.
     *
     * @return string
     */
    public function getUid(): string;

    /**
     * Add a source that depends upon this source.
     *
     * @param Node $node
     */
    public function addEdge(Node $node);

    /**
     * @return Node[]|array
     */
    public function getEdges(): array;

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
    public function isSame(Node $node): bool;
}
