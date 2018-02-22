<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\Collections\Collection;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Tests\TestCase;

class CollectionsNTest extends TestCase
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

    public function testArrayContainerCollection1D()
    {
        $collection = new Collection();
        $this->assertEquals(0, $collection->count());

        $arr = ['A' => 'B', 'B' => 'C', 'C' => 'D'];
        $collection = new Collection($arr);
        $this->assertEquals(3, $collection->count());

        $this->assertEquals('B', $collection['A']);
        $this->assertEquals('C', $collection['B']);
        $this->assertEquals('D', $collection['C']);
        $this->assertEquals($arr, $collection->all());

        $this->assertEquals(true, isset($collection['A']));
        $this->assertEquals(true, isset($collection['B']));
        $this->assertEquals(true, isset($collection['C']));
        $this->assertEquals(false, isset($collection['D']));

        $collection['D'] = 'E';
        $this->assertEquals('E', $collection['D']);

        unset($collection['A']);
        $this->assertEquals(false, isset($collection['A']));

        $check = [];

        foreach ($collection as $key => $value){
            $check[$key] = $value;
        }

        $this->assertEquals(['B' => 'C', 'C' => 'D', 'D' => 'E'], $check);
        $this->assertEquals(['B' => 'C', 'C' => 'D', 'D' => 'E'], $collection->toArray());
        $this->assertEquals('{"B":"C","C":"D","D":"E"}', $collection->toJson());

        $collection->filterKeys(['B' => true, 'C' => true]);
        $this->assertEquals(['D' => 'E'], $collection->toArray());

        $collection = new FlatCollection([
            'London Bridge' => 5,
            'London Tower' => 2,
            'St Pauls' => 3,
            'BBC' => 1,
            'Green Water' => -8
        ]);

        $this->assertEquals(['London Bridge' => 5, 'London Tower' => 2], $collection->find('London'));
        $this->assertEquals(['BBC' => 1], $collection->find('BB'));
        $this->assertEquals(['Green Water' => -8], $collection->find('Water'));

        $collection = $collection->sort(function($a, $b){

            if ($a <= $b){
                return 1;
            }else{
                return -1;
            }
        });

        $check = [
            'London Bridge' => 5,
            'St Pauls' => 3,
            'London Tower' => 2,
            'BBC' => 1,
            'Green Water' => -8
        ];
        $this->assertEquals($check, $collection->all());
    }
}
