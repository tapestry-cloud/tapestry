<?php

namespace Tapestry\Entities\Tree;

/**
 * Class Tree.
 *
 * Code based upon the Tree data structure of Itsy Bitsy data structure by Jamie <https://jamie.build/>
 *
 * @see https://github.com/jamiebuilds/itsy-bitsy-data-structures
 */
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
     * @return void
     */
    public function traverse(callable $callback, $node = null)
    {
        if (is_null($node)) {
            $this->traverse($callback, $this->root);

            return;
        }

        $callback($node);

        foreach ($node->getChildren() as $child) {
            $this->traverse($callback, $child);
        }
    }

    /**
     * Helper method for traversing the tree and counting all the Leaf nodes.
     *
     * @return int
     */
    public function childCount(): int
    {
        $count = 0;
        $this->traverse(function () use (&$count) {
            $count++;
        });

        return $count;
    }

    /**
     * Return all symbols stored in this Tree as a List.
     *
     * @return array|Symbol[]
     */
    public function getAllSymbols(): array
    {
        $symbols = [];
        $this->traverse(function (Leaf $leaf) use (&$symbols) {
            if (! isset($symbols[$leaf->getId()])) {
                $symbols[$leaf->getId()] = $leaf->getSymbol();
            }
        });

        return $symbols;
    }

    /**
     * Add a new item to the tree.
     *
     * @param Leaf $leaf
     * @param string|null $parent
     * @return void
     */
    public function add(Leaf $leaf, $parent = null)
    {
        if (is_null($this->root)) {
            $this->root = $leaf;

            return;
        }

        if (! is_null($parent)) {
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
