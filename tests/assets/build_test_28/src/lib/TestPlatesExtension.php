<?php namespace TapestryCloud\Lib;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class TestPlatesExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('test', [$this, 'test']);
    }

    public function test()
    {
        return 'hello world!';
    }
}