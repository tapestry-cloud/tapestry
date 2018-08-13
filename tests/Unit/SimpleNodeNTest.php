<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Tests\TestCase;

class SimpleNodeNTest extends TestCase
{
    /**
     * Unit Test of the SimpleNode class.
     *
     * @throws \Tapestry\Exceptions\GraphException
     */
    public function testSimpleNodeClass()
    {
        $class = new SimpleNode('test', 'hello world');
        $this->assertEquals('test', $class->getUid());
        $this->assertEquals('hello world', $class->getHash());
        $this->assertEquals([], $class->getEdges());

        $this->assertTrue($class->isSame($class));
        $this->assertTrue($class->isSame(new SimpleNode('test', 'hello world')));
    }

    /**
     * Unit Test of the SimpleNode class exception state.
     *
     * @throws \Tapestry\Exceptions\GraphException
     */
    public function testSimpleNodeClassException()
    {
        $class = new SimpleNode('test', 'hello world');
        $this->expectExceptionMessage('Node being compared must have the same identifier.');
        $this->assertFalse($class->isSame(new SimpleNode('hello world', 'test')));
    }
}
