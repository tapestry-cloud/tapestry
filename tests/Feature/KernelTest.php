<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Providers\ProjectKernelServiceProvider;
use Tapestry\Tests\Traits\MockTapestry;

class KernelTest extends CommandTestBase
{

    use MockTapestry;

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

    /**
     * Written for issue #135
     * @link https://github.com/carbontwelve/tapestry/issues/135
     */
    public function testLoadingCommandViaSiteKernelBoot()
    {
        $this->copyDirectory('assets/build_test_10/src', '_tmp');
        $output = $this->runCommand('hello');

        $this->assertEquals('Hello world! This command was loaded via a site Kernel.', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());
    }

    /**
     * Written for issue #234
     * @link https://github.com/carbontwelve/tapestry/issues/234
     * @expectedException \Exception
     * @expectedExceptionMessage [SiteThirtySeven\Kernel] kernel file not found.
     */
    public function testKernelThrowsException()
    {
        $this->copyDirectory('assets/build_test_37/src', '_tmp');
        $class = new ProjectKernelServiceProvider();
        $tapestry = $this->mockTapestry(__DIR__ . DIRECTORY_SEPARATOR . '_tmp');
        $class->setContainer($tapestry->getContainer());

        $this->expectExceptionMessage('[SiteThirtySeven\Kernel] kernel file not found.');
        $class->boot();
    }

    /**
     * Written for issue #235
     * @link https://github.com/carbontwelve/tapestry/issues/235
     */
    public function testKernelCaseLoaded()
    {
        $this->copyDirectory('assets/build_test_38/src', '_tmp');
        $class = new ProjectKernelServiceProvider();
        $tapestry = $this->mockTapestry(__DIR__ . DIRECTORY_SEPARATOR . '_tmp');
        $class->setContainer($tapestry->getContainer());

        $class->boot();
        $kernel = $tapestry->getContainer()->get(KernelInterface::class);
        $this->assertInstanceOf('\SiteThirtyEight\Kernel', $kernel);
    }
}
