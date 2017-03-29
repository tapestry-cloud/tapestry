<?php

namespace Tapestry\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ContentType;
use Tapestry\Entities\File;
use Tapestry\Modules\ContentTypes\ContentTypeFactory;

class KernelTest extends CommandTestBase
{

    /**
     * Written for issue #133
     * @link https://github.com/carbontwelve/tapestry/issues/133
     */
    public function testKernelInterfaceMethodsUsed()
    {
        $this->copyDirectory('assets/build_test_8/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_8/check/boot.html',
            __DIR__.'/_tmp/build_local/boot.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_8/check/register.html',
            __DIR__.'/_tmp/build_local/register.html',
            '',
            true
        );
    }

    public function testSiteKernelLoading()
    {
        $this->copyDirectory('assets/build_test_28/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_28/check/index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );
    }
}
