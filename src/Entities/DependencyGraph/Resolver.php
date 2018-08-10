<?php

namespace Tapestry\Entities\DependencyGraph;

use Closure;

class Resolver
{
    /**
     * @var Node[]
     */
    private $resolved = [];

    /**
     * @var Node[]
     */
    private $unresolved = [];

    /**
     * @var array
     */
    private $adjacencyList = [];

    /**
     * @param Node $node
     * @return array
     * @throws \Exception
     */
    public function resolve(Node $node): array
    {
        $this->resolved = [];
        $this->unresolved = [];
        $this->adjacencyList = [];
        $this->resolveNode($node);
        return $this->resolved;
    }

    /**
     * Returns the resolved graph adjacency list.
     *
     * @return array
     */
    public function getAdjacencyList(): array
    {
        return $this->adjacencyList;
    }

    /**
     * Reduces the resolved graph and returns only nodes that have their
     * changed flag set to true or are connected as dependants to
     * a node that has its changed flag set to true.
     *
     * @param Closure $closure
     * @return array
     */
    public function reduce(Closure $closure): array
    {
        $modified = [];

        foreach ($this->resolved as $node){ // @todo have this use a passed closure to do the evaluation
            if ($closure($node) === true){
                array_push($modified, $node);
                foreach ($this->adjacencyList[$node->getUid()] as $affected) {
                    array_push($modified,$affected);
                }
            }
        }

        return $modified;
    }

    /**
     * @param Node $node
     * @param Node[] $parents
     * @throws \Exception
     */
    private function resolveNode(Node $node, $parents = [])
    {
        if (! isset($this->adjacencyList[$node->getUid()])){
            $this->adjacencyList[$node->getUid()] = [];
        }

        array_push($this->unresolved, $node);
        foreach ($node->getEdges() as $edge)
        {
            if (! in_array($edge, $this->resolved)){
                if (in_array($edge, $this->unresolved)){
                    throw new \Exception('Circular reference detected: ' . $node->getUid() . ' -> '. $edge->getUid());
                }
                array_push($parents, $node);
                $this->resolveNode($edge, $parents);
            }
        }
        foreach($parents as $p){
            if ($node->getUid() !== $p->getUid() && !in_array($node, $this->adjacencyList[$p->getUid()])) {
                array_push($this->adjacencyList[$p->getUid()], $node);
            }
        }
        array_push($this->resolved, $node);
        if (($key = array_search($node, $this->unresolved)) !== false) {
            unset($this->unresolved[$key]);
        }
    }
}