<?php

namespace Tapestry\Entities\Tree;

class Tree
{
    /**
     * @var null|Leaf
     */
    private $root = null;

    /**
     * Traverse all items within the tree and execute the callback for
     * each node in the tree. The $node param is only required for the
     * recursive nature of the function, to use this method simply pass
     * a valid callable.
     *
     * @param callable $callback
     * @param null|array|Leaf $node
     */
    public function traverse(callable $callback, $node = null)
    {
        if (is_null($node)) {
            $this->traverse($callback, $this->root);
            return;
        }

        $callback($node);

        foreach($node->getChildren() as $child) {
            $this->traverse($callback, $child);
        }
    }

    /**
     * Add a new item to the tree.
     *
     * @param Leaf $leaf
     * @param string|null $parent
     */
    public function add(Leaf $leaf, $parent = null)
    {
        if (is_null($this->root)) {
            $this->root = $leaf;
            return;
        }

        if (!is_null($parent)) {
            $this->traverse(function (Leaf $node) use ($parent, $leaf) {

                if ($node->getId() === $parent) {
                    $node->addChild($leaf);
                }
            });
        }
    }

    /**
     * @return null|Leaf
     */
    public function getRoot()
    {
        return $this->root;
    }
}