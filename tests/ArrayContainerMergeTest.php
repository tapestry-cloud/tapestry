<?php namespace Tapestry\Tests;

use Tapestry\ArrayContainer;

class ArrayContainerMergeTest extends CommandTestBase
{
    public function testArrayContainerClassBaseFunctionality()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123
            ],
            'd' => 'hello'
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
                'world'
            ]
        ]);

        $this->assertEquals([
            'c' => 123,
            'hello',
            'world'
        ], $arrayContainer->get('b'));
    }

    /**
     * Test `merge` method successfully merges
     */
    public function testArrayMerge()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123
            ],
            'd' => 'hello'
        ]);

        $this->assertEquals(true, $arrayContainer->get('a'));
        $this->assertEquals(['c' => 123], $arrayContainer->get('b'));
        $this->assertEquals('hello', $arrayContainer->get('d'));

        $arrayContainer->merge([
            'a' => false,
            'b' => [
                'c' => 321,
                'c1' => 'Hello world!'
            ],
            'e' => 'Test'
        ]);

        $this->assertEquals(false, $arrayContainer->get('a'));
        $this->assertEquals(['c' => 321, 'c1' => 'Hello world!'], $arrayContainer->get('b'));
        $this->assertEquals('Hello world!', $arrayContainer->get('b.c1'));
        $this->assertEquals('hello', $arrayContainer->get('d'));
        $this->assertEquals('Test', $arrayContainer->get('e'));
    }

    /**
     * Test that `has` method works in both single and dot notation modes
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
     * Test that `set` method works in both single and dot notation modes as well as correctly merges input
     */
    public function testMultiDimensionDotSet()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123
            ],
            'd' => 'hello'
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
     * Test that `remove` method works in both single and dot notation modes
     */
    public function testMultiDimensionDotRemove()
    {
        $arrayContainer = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123,
                'e' => 'Test_B',
                'f' => [
                    'hello' => 'world',
                    'world' => 'hello'
                ]
            ],
            'd' => 'hello'
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
}