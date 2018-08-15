<?php

namespace Tapestry\Entities\Tree;

/**
 * Class TreeShaker.
 *
 * Given two Tree structures, using the isSame method on each Leaf's Symbol
 * this class reduces the Tree to just those nodes that are modified or
 * affected by ancestors that are modified.
 */
class TreeShaker
{
    /**
     * This method assumes that Tree $a is the cached version and Tree $b is
     * the fresh data with which to run a comparison.
     *
     * It traverses each Tree and shakes loose nodes that are modified before
     * compiling a list of Symbols that are either modified or affected by
     * ancestors that are modified. Tapestry can then use that list to compile
     * just the files affected by modification.
     *
     * @param Tree $a
     * @param Tree $b
     * @return array|Symbol[]
     */
    public function reduce(Tree $a, Tree $b): array
    {
        $symbols = $b->getAllSymbols();
        $changed = [];

        $a->traverse(function (Leaf $leaf) use ($symbols, &$changed) {
            if (isset($symbols[$leaf->getId()])) {
                if (! $leaf->getSymbol()->isSame($symbols[$leaf->getId()])) {
                    $changed[$leaf->getId()] = $leaf->getSymbol();
                    if ($leaf->hasChildren()) {
                        $changed = array_merge($changed, $this->traverse($leaf));
                    }
                }
            }
        });

        return $changed;
    }

    /**
     * Recursive lookup of Tree Leaves to be added to the $changed array.
     *
     * @param Leaf $leaf
     * @param array $changed
     * @return array
     */
    private function traverse(Leaf $leaf, array $changed = []): array
    {
        foreach ($leaf->getChildren() as $child) {
            $changed[$child->getId()] = $child->getSymbol();
            if ($child->hasChildren()) {
                $changed = $this->traverse($child, $changed);
            }
        }

        return $changed;
    }
}
