<?php

namespace Tapestry\Tests;

use Tapestry\Entities\Collections\FlatCollection;

class CollectionsTest extends CommandTestBase
{
    public function testFlatCollection()
    {
        $collection = new FlatCollection();
        $this->assertEquals(0, $collection->count());

        $collection->set('A', 'B');
        $collection->set('B', 'C');

        $this->assertEquals(2, $collection->count());

        $this->assertEquals('B', $collection->get('A'));
        $this->assertEquals('C', $collection->get('B'));

        $this->assertEquals(null, $collection->get('C'));
        $this->assertEquals(false, $collection->get('C', false));
        $this->assertEquals('D', $collection->get('C', 'D'));
    }
}
