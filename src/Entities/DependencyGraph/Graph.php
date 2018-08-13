<?php

namespace Tapestry\Entities\DependencyGraph;

use Tapestry\Exceptions\GraphException;

class Graph
{

    /**
     * @var Node
     */
    private $root;

    /**
     * uid -> obj node lookup table
     *
     * @var Node[]
     */
    private $table = [];

    /**
     * Graph constructor.
     * @param Node|null $root
     */
    public function __construct(Node $root = null)
    {
        if (! is_null($root)){
            $this->setRoot($root);
        }
    }

    /**
     * This method acts to reset the Graph before
     * setting the root node.
     *
     * @param Node $node
     */
    public function setRoot(Node $node)
    {
        $this->table = [];
        $this->root = $node;
        $this->table[$node->getUid()] = $node;
    }

    /**
     * @param string $uid
     * @param Node $node
     * @throws GraphException
     */
    public function addEdge(string $uid, Node $node)
    {
        if (! isset($this->table[$uid])){
            throw new GraphException('The edge ['.$uid.'] is not found in graph.');
        }
        $this->table[$uid]->addEdge($node);
        $this->table[$node->getUid()] = $node;
    }

    /**
     * @param string $uid
     * @return Node
     * @throws GraphException
     */
    public function getEdge(string $uid): Node
    {
        if (! isset($this->table[$uid])){
            throw new GraphException('The edge ['.$uid.'] is not found in graph.');
        }

        return $this->table[$uid];
    }
}