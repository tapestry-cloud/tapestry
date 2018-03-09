<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Tests\TestCase;

class TreeNTest extends TestCase
{
    public function testTreeClass()
    {
        $tree = new Tree();
        $root = new Leaf('root', 'Kernel');
        $tree->add($root);
        $this->assertSame($root, $tree->getRoot());

        $rootLeafA = new Leaf('root.a', 'Root_Leaf_A');
        $tree->add($rootLeafA, 'root');
        $rootLeafB = new Leaf('root.b', 'Root_Leaf_B');
        $tree->add($rootLeafB, 'root');
        $rootLeafBC = new Leaf('root.b.c', 'Root_Leaf_B_C');
        $tree->add($rootLeafBC, 'root.b');

        $this->assertEquals(2, $tree->getRoot()->childCount());

        $check = ['root', 'root.a', 'root.b', 'root.b.c'];
        $arr = [];

        $tree->traverse(function (Leaf $leaf) use ($check, &$arr) {
            $arr[] = $leaf->getId();
            $this->assertTrue(in_array($leaf->getId(), $check));
        });

        $this->assertCount(4, $arr);
        $this->assertEquals(4, $tree->childCount());
    }

    /**
     * Example AST Tree:
     *
     * ├── kernel.php
     * |   ├── Content Type A
     * |   |   ├── File A
     * |   |   ├── File B
     * |   |   └── File C
     * |   ├── Content Type B
     * |   |   ├── File D
     * |   |   └── File E
     * |   ├── Template A
     * |   |   ├── File A
     * |   |   ├── View A
     * |   |   |   ├── File B
     * |   |   |   └── File C
     * |   |   └── File D
     * |   └── Template B
     * |       └── File E
     * └── config.php
     *    └── *All the same nodes as kernel.php
     *
     * If only Template B changes then only File E needs to be re-generated and the tree will reduce to:
     *
     * └── Template B
     *    └── File E
     *
     * Resulting in only one file needing to be regenerated.
     *
     * Once generated the AST will be cached by Tapestry and amended as files are added/removed from
     * the project workspace.
     */
    //public function testAST()
    //{
        // @todo
    //}
}