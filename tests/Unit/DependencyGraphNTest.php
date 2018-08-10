<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\DependencyGraph\Resolver;
use Tapestry\Entities\DependencyGraph\Node;
use Tapestry\Modules\Source\AbstractSource;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class DependencyGraphNTest extends TestCase {

    public function testResolver()
    {
        try{
            /** @var Node[] $nodes */
            $nodes = [];
            foreach (range('a', 'e') as $letter) {
                $nodes[$letter] = new MemorySource('memory_' . $letter, 'Howdy!', 'memory.md', 'md', 'memory/' . $letter, 'memory/' . $letter . '/memory.md');
            }
            $nodes['a']->addEdge($nodes['b']); // b depends on a
            $nodes['a']->addEdge($nodes['d']); // d depends on a
            $nodes['b']->addEdge($nodes['c']); // c depends on b
            $nodes['b']->addEdge($nodes['e']); // e depends on b
            $nodes['c']->addEdge($nodes['d']); // d depends on c
            $nodes['c']->addEdge($nodes['e']); // e depends on c

            $class = new Resolver();
            $result = $class->resolve($nodes['a']);

            $this->assertSame(['memory_d', 'memory_e', 'memory_c', 'memory_b', 'memory_a'], array_map(function(Node $v){
                return $v->getUid();
            }, $result));
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

    public function testResolverCircularDetection()
    {
        try{
            /** @var Node[] $nodes */
            $nodes = [];
            foreach (range('a', 'e') as $letter) {
                $nodes[$letter] = new MemorySource('memory_' . $letter, 'Howdy!', 'memory.md', 'md', 'memory/' . $letter, 'memory/' . $letter . '/memory.md');
            }
            $nodes['a']->addEdge($nodes['b']); // b depends on a
            $nodes['a']->addEdge($nodes['d']); // d depends on a
            $nodes['b']->addEdge($nodes['c']); // c depends on b
            $nodes['b']->addEdge($nodes['e']); // e depends on b
            $nodes['c']->addEdge($nodes['d']); // d depends on c
            $nodes['c']->addEdge($nodes['e']); // e depends on c
            $nodes['d']->addEdge($nodes['b']); // b depends on d - circular

            $class = new Resolver();
            $this->expectExceptionMessage('Circular reference detected: memory_d -> memory_b');
            $class->resolve($nodes['a']);
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

    public function testGraphAdjacencyList()
    {
        try{
            /** @var Node[] $nodes */
            $nodes = [];
            foreach (range('a', 'e') as $letter) {
                $nodes[$letter] = new MemorySource('memory_' . $letter, 'Howdy!', 'memory.md', 'md', 'memory/' . $letter, 'memory/' . $letter . '/memory.md');
            }

            $nodes['a']->addEdge($nodes['b']); // b depends on a
            $nodes['a']->addEdge($nodes['d']); // d depends on a
            $nodes['b']->addEdge($nodes['c']); // c depends on b
            $nodes['b']->addEdge($nodes['e']); // e depends on b
            $nodes['c']->addEdge($nodes['d']); // d depends on c
            $nodes['c']->addEdge($nodes['e']); // e depends on c

            $class = new Resolver();
            $class->resolve($nodes['a']);

            $this->assertSame([
                'memory_a' => ['memory_d','memory_e','memory_c','memory_b'],
                'memory_b' => ['memory_d','memory_e','memory_c'],
                'memory_c' => ['memory_d','memory_e'],
                'memory_d' => [],
                'memory_e' => [],
            ], array_map(function(array $v){
                return array_map(function(Node $n){
                    return $n->getUid();
                }, $v);
            }, $class->getAdjacencyList()));
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

    public function testGraphReduction()
    {
        try{
            /** @var MemorySource[] $nodes */
            $nodes = [];
            foreach (range('a', 'f') as $letter) {
                $nodes[$letter] = new MemorySource('memory_' . $letter, 'Howdy!', 'memory.md', 'md', 'memory/' . $letter, 'memory/' . $letter . '/memory.md');
            }

            $nodes['c']->setHasChanged();

            $nodes['a']->addEdge($nodes['b']); // b depends on a
            $nodes['a']->addEdge($nodes['d']); // d depends on a
            $nodes['b']->addEdge($nodes['c']); // c depends on b
            $nodes['b']->addEdge($nodes['e']); // e depends on b
            $nodes['c']->addEdge($nodes['d']); // d depends on c
            $nodes['c']->addEdge($nodes['e']); // e depends on c

            $reduce = function (AbstractSource $source) {
                return $source->hasChanged();
            };

            $class = new Resolver();
            $class->resolve($nodes['a']);
            $reduced = $class->reduce($reduce);

            $this->assertCount(3, $reduced);
            $this->assertSame([$nodes['c'],$nodes['d'], $nodes['e']], $reduced);

            $nodes['e']->addEdge($nodes['f']); // f depends on e
            $class = new Resolver();
            $class->resolve($nodes['a']);
            $reduced = $class->reduce($reduce);

            $this->assertCount(4, $reduced);
            $this->assertSame([$nodes['c'],$nodes['d'], $nodes['f'], $nodes['e']], $reduced);
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }
}