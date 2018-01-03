<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Modules\Kernel\KernelInterface;
use Tapestry\Providers\ProjectKernelServiceProvider;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;

class KernelTest extends TestCase
{

    use MockTapestry;

    /**
     * Written for issue #133
     * @link https://github.com/carbontwelve/tapestry/issues/133
     */
    public function testKernelInterfaceMethodsUsed()
    {
        $this->loadToTmp($this->assetPath('build_test_8/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_8/check/boot.html'),
            $this->tmpPath('build_local/boot.html'),
            '',
            true
        );

        $this->assertFileEquals(
            $this->assetPath('build_test_8/check/register.html'),
            $this->tmpPath('build_local/register.html'),
            '',
            true
        );
    }

    public function testSiteKernelLoading()
    {
        $this->loadToTmp($this->assetPath('build_test_28/src'));
        $output = $this->runCommand('build', '--quiet');

        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            $this->assetPath('build_test_28/check/index.html'),
            $this->tmpPath('build_local/index.html'),
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
        $this->loadToTmp($this->assetPath('build_test_10/src'));
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
        $this->loadToTmp($this->assetPath('build_test_37/src'));
        $class = new ProjectKernelServiceProvider();
        $tapestry = $this->mockTapestry($this->tmpPath());
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
        $this->loadToTmp($this->assetPath('build_test_38/src'));
        $class = new ProjectKernelServiceProvider();
        $tapestry = $this->mockTapestry($this->tmpPath());
        $class->setContainer($tapestry->getContainer());

        $class->boot();
        $kernel = $tapestry->getContainer()->get(KernelInterface::class);
        $this->assertInstanceOf('\SiteThirtyEight\Kernel', $kernel);
    }
}
