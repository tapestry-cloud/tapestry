<?php namespace Tapestry\Tests;

use Tapestry\ArrayContainer;

class ArrayContainerMergeTest extends CommandTestBase
{
    public function testArrayContainerClassBaseFunctionality()
    {
        $configuration = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123
            ],
            'd' => 'hello'
        ]);

        $this->assertEquals(true, $configuration->has('a'));
        $this->assertEquals(false, $configuration->has('non_existent_key'));
        $this->assertEquals(null, $configuration->get('non_existent_key'));
        $this->assertEquals('Not Null', $configuration->get('non_existent_key', 'Not Null'));

        $configuration->set('a_key', 'some_value');
        $this->assertEquals(true, $configuration->has('a_key'));
        $this->assertEquals('some_value', $configuration->get('a_key'));

        $configuration->remove('a_key');
        $this->assertEquals(false, $configuration->has('a_key'));
        $this->assertEquals(null, $configuration->get('a_key'));

        $configuration->merge([
            'b' => [
                'hello',
                'world'
            ]
        ]);

        $this->assertEquals([
            'c' => 123,
            'hello',
            'world'
        ], $configuration->get('b'));
    }

    public function testConfigurationMerge()
    {
        $configuration = new ArrayContainer([
            'a' => true,
            'b' => [
                'c' => 123
            ],
            'd' => 'hello'
        ]);

        $this->assertEquals(true, $configuration->get('a'));
        $this->assertEquals(['c' => 123], $configuration->get('b'));
        $this->assertEquals('hello', $configuration->get('d'));

        $configuration->merge([
            'a' => false,
            'b' => [
                'c' => 321,
                'c1' => 'Hello world!'
            ],
            'e' => 'Test'
        ]);

        $this->assertEquals(false, $configuration->get('a'));
        $this->assertEquals(['c' => 321, 'c1' => 'Hello world!'], $configuration->get('b'));
        $this->assertEquals('hello', $configuration->get('d'));
        $this->assertEquals('Test', $configuration->get('e'));
    }
}