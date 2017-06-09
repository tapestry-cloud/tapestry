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

        $url = new Url($configuration);

        $this->assertSame('http://www.example.com/', $url->parse());
        $this->assertSame('http://www.example.com/', $url->parse(''));
        $this->assertSame('http://www.example.com/', $url->parse('/'));

        $this->assertSame('http://www.example.com/?abc=123&def=456', $url->parse('?abc=123&def=456'));
        $this->assertSame('http://www.example.com/?abc=123&def=456', $url->parse('/?abc=123&def=456'));

        $this->assertSame('http://www.example.com/#abc-123', $url->parse('#abc-123'));
        $this->assertSame('http://www.example.com/#abc-123', $url->parse('/#abc-123'));

        $this->assertSame('http://www.example.com/abc/', $url->parse('abc'));
        $this->assertSame('http://www.example.com/abc/', $url->parse('/abc'));
        $this->assertSame('http://www.example.com/abc/', $url->parse('/abc/'));

        $this->assertSame('http://www.example.com/abc/123/', $url->parse('abc/123'));
        $this->assertSame('http://www.example.com/abc/123/', $url->parse('/abc/123'));
        $this->assertSame('http://www.example.com/abc/123/', $url->parse('/abc/123/'));

        $this->assertSame('http://www.example.com/abc/123/?abc=123&def=456', $url->parse('abc/123?abc=123&def=456'));
        $this->assertSame('http://www.example.com/abc/123/?abc=123&def=456', $url->parse('/abc/123?abc=123&def=456'));
        $this->assertSame('http://www.example.com/abc/123/?abc=123&def=456', $url->parse('/abc/123/?abc=123&def=456'));

        $this->assertSame('http://www.example.com/abc/123/#abc-123', $url->parse('abc/123#abc-123'));
        $this->assertSame('http://www.example.com/abc/123/#abc-123', $url->parse('/abc/123#abc-123'));
        $this->assertSame('http://www.example.com/abc/123/#abc-123', $url->parse('/abc/123/#abc-123'));

        $this->assertEquals('http://www.example.com/abc/', $url->parse('abc/index.html'));
        $this->assertEquals('http://www.example.com/abc/123/', $url->parse('abc/123/index.html'));
        $this->assertEquals('http://www.example.com/abc/123.html', $url->parse('abc/123.html'));

        $this->assertEquals('http://www.example.com/0/', $url->parse(0));
        $this->assertEquals('http://www.example.com/', $url->parse(null));
        $this->assertEquals('http://www.example.com/-1/', $url->parse(-1));
        $this->assertEquals('http://www.example.com/3.33/', $url->parse(3.33));

        $this->assertSame('http://www.example.com/hello%20world/', $url->parse('hello world'));
        $this->assertSame('http://www.example.com/hello%20world/', $url->parse('/hello world'));
        $this->assertSame('http://www.example.com/hello%20world/', $url->parse('/hello world/'));

        $this->assertSame('http://www.example.com/hello%20world/this%20is%20a%20test/', $url->parse('hello world/this is a test'));
        $this->assertSame('http://www.example.com/hello%20world/this%20is%20a%20test/', $url->parse('/hello world/this is a test'));
        $this->assertSame('http://www.example.com/hello%20world/this%20is%20a%20test/', $url->parse('/hello world/this is a test/'));

        $this->assertSame('http://www.example.com/hello/world', $url->parse('hello/world/index.html'));
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
