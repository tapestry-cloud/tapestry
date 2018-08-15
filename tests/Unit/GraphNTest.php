<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\DependencyGraph\Graph;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Tests\TestCase;

class GraphNTest extends TestCase
{
    /**
     * Unit Test of the Graph class.
     */
    public function testGraphClass()
    {
        /** @var SimpleNode[] $nodes */
        $nodes = [];
        foreach (range('a', 'f') as $letter) {
            $nodes[$letter] = new SimpleNode($letter, 'letter_' . $letter);
        }

        try {
            $graph = new Graph($nodes['a']);
            $graph->addEdge('a', $nodes['b']); // b depends on a
            $graph->addEdge('a', $nodes['d']); // d depends on a
            $graph->addEdge('b', $nodes['c']); // c depends on b
            $graph->addEdge('b', $nodes['e']); // e depends on b
            $graph->addEdge('c', $nodes['d']); // d depends on c
            $graph->addEdge('c', $nodes['e']); // e depends on c
            $graph->addEdge('a', $nodes['f']); // e depends on c

            foreach (range('a', 'f') as $letter) {
                $this->assertSame($nodes[$letter], $graph->getEdge($letter));
            }

            $this->assertCount(3, $graph->getEdge('a')->getEdges());
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

    /**
     * Unit Test of the Graph class exception state.
     *
     * @throws \Tapestry\Exceptions\GraphException
     */
    public function testGraphClassExceptionThrown()
    {
        $graph = new Graph();
        $this->expectExceptionMessage('The edge [a] is not found in graph.');
        $graph->addEdge('a', new SimpleNode('temp', 'temp'));

        $graph = new Graph();
        $this->expectExceptionMessage('The edge [a] is not found in graph.');
        $graph->getEdge('a');
    }
}
