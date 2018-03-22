<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\Tree\Leaf;
use Tapestry\Entities\Tree\Symbol;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Entities\Tree\TreeShaker;
use Tapestry\Entities\Tree\TreeToASCII;
use Tapestry\Tests\TestCase;

class TreeNTest extends TestCase
{
    public function testTreeClass()
    {
        $tree = new Tree();
        $root = new Leaf('root', new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100));
        $tree->add($root);
        $this->assertSame($root, $tree->getRoot());

        $rootLeafA = new Leaf('root.a', new Symbol('Root_Leaf_A', Symbol::SYMBOL_CONTENT_TYPE, 100));
        $tree->add($rootLeafA, 'root');
        $rootLeafB = new Leaf('root.b', new Symbol('Root_Leaf_B', Symbol::SYMBOL_CONTENT_TYPE, 100));
        $tree->add($rootLeafB, 'root');
        $rootLeafBC = new Leaf('root.b.c', new Symbol('Root_Leaf_B_C', Symbol::SYMBOL_SOURCE, 100));
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
    public function testASTReduce()
    {
        $treeA = new Tree();
        $treeA->add(new Leaf('kernel', new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $treeA->add(new Leaf('kernel.config', new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 100)), 'kernel');
        $treeA->add(new Leaf('kernel.config.content_type_blog', new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100)), 'kernel.config');
        $treeA->add(new Leaf('kernel.config.content_type_blog.blog_view', new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog');
        $treeA->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_a', new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');
        $treeA->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_b', new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');

        $treeB = new Tree();
        $treeB->add(new Leaf('kernel', new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $treeB->add(new Leaf('kernel.config', new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 100)), 'kernel');
        $treeB->add(new Leaf('kernel.config.content_type_blog', new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100)), 'kernel.config');
        $treeB->add(new Leaf('kernel.config.content_type_blog.blog_view', new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 150)), 'kernel.config.content_type_blog');
        $treeB->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_a', new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');
        $treeB->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_b', new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');

        $shaker = new TreeShaker();
        $list = $shaker->reduce($treeA, $treeB);
        $this->assertEquals(3, count($list));

        $treeC = new Tree();
        $treeC->add(new Leaf('kernel', new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $treeC->add(new Leaf('kernel.config', new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 100)), 'kernel');
        $treeC->add(new Leaf('kernel.config.content_type_blog', new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100)), 'kernel.config');
        $treeC->add(new Leaf('kernel.config.content_type_blog.blog_view', new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog');
        $treeC->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_a', new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');
        $treeC->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_b', new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');

        $list = $shaker->reduce($treeA, $treeC);
        $this->assertEquals(0, count($list));

        $treeD = new Tree();
        $treeD->add(new Leaf('kernel', new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $treeD->add(new Leaf('kernel.config', new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 150)), 'kernel');
        $treeD->add(new Leaf('kernel.config.content_type_blog', new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100)), 'kernel.config');
        $treeD->add(new Leaf('kernel.config.content_type_blog.blog_view', new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog');
        $treeD->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_a', new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');
        $treeD->add(new Leaf('kernel.config.content_type_blog.blog_view.blog_page_b', new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100)), 'kernel.config.content_type_blog.blog_view');

        $list = $shaker->reduce($treeA, $treeD);
        $this->assertEquals(5, count($list));
    }

    public function testAddSymbol()
    {
        $treeA = new Tree();
        $this->assertTrue($treeA->addSymbol(new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $this->assertTrue($treeA->addSymbol(new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 100), 'kernel'));
        $this->assertTrue($treeA->addSymbol(new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100), 'configuration'));
        $this->assertTrue($treeA->addSymbol(new Symbol('content_type_default', Symbol::SYMBOL_CONTENT_TYPE, 100), 'configuration'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100), 'content_type_blog'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_c', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_d', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('template_a', Symbol::SYMBOL_SOURCE, 100), 'content_type_default'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100), 'template_a'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_c', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_d', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeA->addSymbol(new Symbol('blog_page_e', Symbol::SYMBOL_SOURCE, 100), 'template_a'));

        echo 'Tree A' . PHP_EOL;
        echo (new TreeToASCII($treeA));

        $treeB = new Tree();
        $this->assertTrue($treeB->addSymbol(new Symbol('kernel', Symbol::SYMBOL_KERNEL, 100)));
        $this->assertTrue($treeB->addSymbol(new Symbol('configuration', Symbol::SYMBOL_CONFIGURATION, 100), 'kernel'));
        $this->assertTrue($treeB->addSymbol(new Symbol('content_type_blog', Symbol::SYMBOL_CONTENT_TYPE, 100), 'configuration'));
        $this->assertTrue($treeB->addSymbol(new Symbol('content_type_default', Symbol::SYMBOL_CONTENT_TYPE, 100), 'configuration'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100), 'content_type_blog'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_c', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_d', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('template_a', Symbol::SYMBOL_SOURCE, 150), 'content_type_default'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_view', Symbol::SYMBOL_SOURCE, 100), 'template_a'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_a', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_b', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_c', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_d', Symbol::SYMBOL_SOURCE, 100), 'blog_view'));
        $this->assertTrue($treeB->addSymbol(new Symbol('blog_page_e', Symbol::SYMBOL_SOURCE, 100), 'template_a'));

        $shaker = new TreeShaker();
        $list = $shaker->reduce($treeA, $treeB);
        $this->assertEquals(7, count($list));
    }
}