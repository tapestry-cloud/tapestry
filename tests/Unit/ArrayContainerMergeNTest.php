<?php

namespace Tapestry\Tests\Unit;

use Tapestry\ArrayContainer;
use Tapestry\Tests\Mocks\MockArrayAccessByKeyClass;
use Tapestry\Tests\TestCase;

class ArrayContainerMergeNTest extends TestCase
{
    public function testArrayContainerClassBaseFunctionality()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123,
            ],
            'd' => 'hello',
        ]);

        $this->assertEquals(true, $arrayContainer->has('a'));
        $this->assertEquals(false, $arrayContainer->has('non_existent_key'));
        $this->assertEquals(null, $arrayContainer->get('non_existent_key'));
        $this->assertEquals('Not Null', $arrayContainer->get('non_existent_key', 'Not Null'));

        $arrayContainer->set('a_key', 'some_value');
        $this->assertEquals(true, $arrayContainer->has('a_key'));
        $this->assertEquals('some_value', $arrayContainer->get('a_key'));

        $arrayContainer->remove('a_key');
        $this->assertEquals(false, $arrayContainer->has('a_key'));
        $this->assertEquals(null, $arrayContainer->get('a_key'));

        $arrayContainer->merge([
            'b' => [
                'hello',
                'world',
            ],
        ]);

        $this->assertEquals([
            'c' => 123,
            'hello',
            'world',
        ], $arrayContainer->get('b'));
    }

    /**
     * Test `merge` method successfully merges.
     */
    public function testArrayMerge()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123,
            ],
            'd' => 'hello',
        ]);

        $this->assertEquals(true, $arrayContainer->get('a'));
        $this->assertEquals(['c' => 123], $arrayContainer->get('b'));
        $this->assertEquals('hello', $arrayContainer->get('d'));

        $arrayContainer->merge([
            'a' => false,
            'b' => [
                'c'  => 321,
                'c1' => 'Hello world!',
            ],
            'e' => 'Test',
        ]);

        $this->assertEquals(false, $arrayContainer->get('a'));
        $this->assertEquals(['c' => 321, 'c1' => 'Hello world!'], $arrayContainer->get('b'));
        $this->assertEquals('Hello world!', $arrayContainer->get('b.c1'));
        $this->assertEquals('hello', $arrayContainer->get('d'));
        $this->assertEquals('Test', $arrayContainer->get('e'));
    }

    /**
     * Test that `has` method works in both single and dot notation modes.
     */
    public function testHas()
    {
        $arrayContainer = new ArrayContainer();
        $this->assertEquals(false, $arrayContainer->has('A_Test'));
        $this->assertEquals(false, $arrayContainer->has('A_Test.B_Test'));
        $this->assertEquals(false, $arrayContainer->has('C_Test'));

        $arrayContainer->set('A_Test.B_Test', 'A_Test-B_Test-Test');
        $arrayContainer->set('C_Test', 'C_Test-Test');

        $this->assertEquals(true, $arrayContainer->has('A_Test'));
        $this->assertEquals(true, $arrayContainer->has('A_Test.B_Test'));
        $this->assertEquals(true, $arrayContainer->has('C_Test'));

        $this->assertEquals('A_Test-B_Test-Test', $arrayContainer->get('A_Test.B_Test'));
        $this->assertEquals(true, is_array($arrayContainer->get('A_Test')));
        $this->assertEquals('C_Test-Test', $arrayContainer->get('C_Test'));
    }

    /**
     * Test that `set` method works in both single and dot notation modes as well as correctly merges input.
     */
    public function testSet()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123,
            ],
            'd' => 'hello',
        ]);

        $b = $arrayContainer->get('b');
        $this->assertEquals(1, count($b));

        $arrayContainer->set('b.test', 'B_Test');
        $this->assertEquals('B_Test', $arrayContainer->get('b.test'));

        $b = $arrayContainer->get('b');
        $this->assertEquals(2, count($b));
        $this->assertEquals('B_Test', $b['test']);
        $this->assertEquals(123, $b['c']);

        $arrayContainer->set('b.c', 'C_Test');
        $this->assertEquals('C_Test', $arrayContainer->get('b.c'));
    }

    /**
     * Test that `remove` method works in both single and dot notation modes.
     */
    public function testRemove()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123,
                'e' => 'Test_B',
                'f' => [
                    'hello' => 'world',
                    'world' => 'hello',
                ],
            ],
            'd' => 'hello',
        ]);

        $arrayContainer->remove('a');
        $this->assertEquals(false, $arrayContainer->has('a'));

        $arrayContainer->remove('b.c');
        $this->assertEquals(false, $arrayContainer->has('b.c'));

        $arrayContainer->remove('b.f.world');
        $this->assertEquals(false, $arrayContainer->has('b.f.world'));
        $this->assertEquals(true, $arrayContainer->has('b.f'));
        $this->assertEquals(true, $arrayContainer->has('b.f.hello'));

        $arrayContainer->remove('b.f');
        $this->assertEquals(false, $arrayContainer->has('b.f.world'));
        $this->assertEquals(false, $arrayContainer->has('b.f.hello'));
        $this->assertEquals(false, $arrayContainer->has('b.f'));
    }

    /**
     * Test pass-through functionality when value is an instance of `ArrayContainer`.
     */
    public function testNestedArrayContainer()
    {
        $arrayContainer = new ArrayContainer([
            'a' => new ArrayContainer([
                'b' => 'A_B_Test',
                'c' => 'A_C_Test',
                'f' => [
                    'hello' => 'world',
                    'world' => 'hello',
                ],
            ]),
            'd' => 'D_Test',
        ]);

        $this->assertEquals(true, $arrayContainer->has('a'));
        $this->assertEquals(true, $arrayContainer->has('a.b'));
        $this->assertEquals(false, $arrayContainer->has('a.e'));
        $this->assertEquals(true, $arrayContainer->has('d'));

        $arrayContainer->set('a.e', 'A_E_Test');
        $this->assertEquals(true, $arrayContainer->has('a.e'));
        $this->assertEquals('A_E_Test', $arrayContainer->get('a.e'));
        $this->assertInstanceOf(ArrayContainer::class, $arrayContainer->get('a'));

        // Test Nested ArrayContainer with Array (key f)
        $this->assertEquals(true, $arrayContainer->has('a.f'));
        $this->assertEquals(false, $arrayContainer->has('a.f.test'));
        $this->assertEquals(null, $arrayContainer->get('a.f.test'));
        $this->assertEquals('Test_Default', $arrayContainer->get('a.f.test', 'Test_Default'));

        // Test Removal of Nested Set
        $arrayContainer->remove('a.f');
        $this->assertEquals(false, $arrayContainer->has('a.f'));

        $arrayContainer->remove('a.c');
        $this->assertEquals(false, $arrayContainer->has('a.c'));

        $arrayContainer->set('a.b', 'A_B_Replace');
        $this->assertEquals('A_B_Replace', $arrayContainer->get('a.b'));
    }

    /**
     * Test special pass-through when value is a class with method `arrayAccessByKey`.
     */
    public function testArrayAccessByKey()
    {
        $arrayContainer = new ArrayContainer();
        $arrayContainer->set('a', new MockArrayAccessByKeyClass([
            'hello' => 'world',
            'world' => 'hello',
        ]));

        $this->assertEquals('world', $arrayContainer->get('a.hello'));
        $this->assertEquals(true, $arrayContainer->has('a.world'));
        $this->assertEquals('hello', $arrayContainer->get('a.world'));
        $this->assertEquals(false, $arrayContainer->has('a.Test_Has'));
    }
}
