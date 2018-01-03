<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
use Tapestry\Entities\Configuration;
use Tapestry\Tests\Traits\MockTapestry;

class HelpersTest extends CommandTestBase
{
    use MockTapestry;

    /**
     * Written for issue #184
     * @version 1.0.8
     * @link https://github.com/tapestry-cloud/tapestry/issues/184
     */
    public function testConfigHelper()
    {
        $tapestry = $this->mockTapestry();
        $tapestry->getContainer()->add(Configuration::class, new Configuration([
            'test' => 'hello world',
            '1234' => 'abcd'
        ]));

        $this->assertSame('hello world', config('test'));
        $this->assertSame('abcd', config('1234'));
        $this->assertNull(config('missing'));
        $this->assertSame('hello world', config('missing', 'hello world'));
        $this->assertSame(1234, config('missing', 1234));
        $this->assertSame(true, config('missing', true));
        $this->assertSame(false, config('missing', false));
        $this->assertSame(-1, config('missing', -1));

        $this->assertInstanceOf(Configuration::class, config());
    }

    /**
     * Written for issue #184
     * @version 1.0.8
     * @link https://github.com/tapestry-cloud/tapestry/issues/184
     */
    public function testFileSizeConvertHelper()
    {
        $this->assertSame('0 b', file_size_convert(0));
        $this->assertSame('0 b', file_size_convert(null));
        $this->assertSame('0 b', file_size_convert(-1));
        $this->assertSame('0 b', file_size_convert('abc'));
        $this->assertSame('0 b', file_size_convert('FFF'));

        $this->assertSame('1000 b', file_size_convert('1000'));
        $this->assertSame('1000 b', file_size_convert(1000));

        $this->assertSame('1 kb', file_size_convert(1024));
        $this->assertSame('1 mb', file_size_convert(1048576));
        $this->assertSame('1 gb', file_size_convert(1073741824));
        $this->assertSame('1 tb', file_size_convert(1099511627776));
        $this->assertSame('1 pb', file_size_convert(1125899906842624));
    }
}
