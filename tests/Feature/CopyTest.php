<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;

class CopyTest extends CommandTestBase
{
    /**
     * Written for issue #168
     * @version 1.0.8
     * @link https://github.com/carbontwelve/tapestry/issues/168
     */
    public function testWarnOnCopyError()
    {
        $this->copyDirectory('assets/build_test_32/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals('', trim($output->getDisplay()));
        $this->assertEquals(0, $output->getStatusCode());
    }
}
