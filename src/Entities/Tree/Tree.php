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
     * @param null|array|Leaf $parent
     * @param int $depth
     * @return void
     */
    public function traverse(callable $callback, $node = null, $parent = null, int $depth = 0)
    {
        if (is_null($node)) {
            $this->traverse($callback, $this->root);

            return;
        }

        $callback($node, $parent, $depth);

        foreach ($node->getChildren() as $child) {
            $this->traverse($callback, $child, $node, ($depth + 1));
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
     * Add a new Leaf to the tree.
     *
     * @param Leaf $leaf
     * @param string|null $parent
     * @return bool
     */
    public function add(Leaf $leaf, $parent = null): bool
    {
        if (is_null($this->root)) {
            $this->root = $leaf;
            return true;
        }

        if (! is_null($parent)) {
            $inserted = false;
            $this->traverse(function (Leaf $node) use ($parent, $leaf, &$inserted) {
                if ($node->getId() === $parent) {
                    $node->addChild($leaf);
                    $inserted = true;
                }
            });
            return $inserted;
        }
        return false;
    }

    /**
     * Add a new Symbol to the tree.
     * Unlike the `add` method this attaches to parent Leaf nodes based upon
     * their symbol id and not the Leaf node id.
     *
     * @param Symbol $symbol
     * @param string|null $parent
     * @return bool
     */
    public function addSymbol(Symbol $symbol, $parent = null): bool
    {
        if (is_null($this->root)) {
            $this->root = new Leaf('root', $symbol);
            return true;
        }

        if (! is_null($parent)) {
            $inserted = false;
            $this->traverse(function (Leaf $node) use ($parent, $symbol, &$inserted) {
                if ($node->getSymbol()->id === $parent) {
                    $node->addChild(new Leaf($node->getId() . '.' . $symbol->id, $symbol));
                    $inserted = true;
                }
            });
            return $inserted;
        }
        return false;
    }

    /**
     * @return null|Leaf
     */
    public function getRoot()
    {
        return $this->root;
    }
}
