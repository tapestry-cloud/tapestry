<?php

namespace Tapestry\Tests;

use Tapestry\Entities\Configuration;
use Tapestry\Entities\Url;

class EntitiesTest extends CommandTestBase
{
    public function testUrlEntity()
    {
        $configuration = new Configuration([
            'site' => [
                'url' => 'http://www.example.com'
            ]
        ]);

        $urlEntity = new Url($configuration);

        $this->assertEquals('http://www.example.com/', $urlEntity->parse());
        $this->assertEquals('http://www.example.com/abc', $urlEntity->parse('abc'));
        $this->assertEquals('http://www.example.com/abc/', $urlEntity->parse('/abc/'));
        $this->assertEquals('http://www.example.com/abc/123', $urlEntity->parse('abc/123'));

        $this->assertEquals('http://www.example.com/abc', $urlEntity->parse('abc/index.html'));
        $this->assertEquals('http://www.example.com/abc/123', $urlEntity->parse('abc/123/index.html'));
        $this->assertEquals('http://www.example.com/abc/123.html', $urlEntity->parse('abc/123.html'));

        $this->assertEquals('http://www.example.com/0', $urlEntity->parse(0));
        $this->assertEquals('http://www.example.com/', $urlEntity->parse(null));
        $this->assertEquals('http://www.example.com/-1', $urlEntity->parse(-1));
        $this->assertEquals('http://www.example.com/3.33', $urlEntity->parse(3.33));
    }

    public function testUrlEntityThrowsException()
    {
        $configuration = new Configuration([
            'site' => []
        ]);
        $urlEntity = new Url($configuration);
        $this->expectException(\Exception::class);
        $urlEntity->parse();
    }
}
