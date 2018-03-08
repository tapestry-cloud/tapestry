<?php

namespace Tapestry\Tests\Unit;

use PHPUnit\Framework\Constraint\IsEqual;
use Tapestry\Entities\Taxonomy;
use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockFile;

class TreeNTest extends TestCase
{

    public function testTreeClass()
    {

        $tree = new Tree();
        $root = new Leaf('root','Kernel');
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

        $tree->traverse(function(Leaf $leaf) use ($check, &$arr){
            $arr[] = $leaf->getId();
            $this->assertTrue(in_array($leaf->getId(), $check));
        });

        $this->assertCount(4, $arr);
    }
}