<?php

namespace Tapestry\Tests;

class ViewFileTraitTest extends CommandTestBase
{
    public function testIsDraftHelper()
    {
        $this->copyDirectory('assets/build_test_15/src', '_tmp');
        $output = $this->runCommand('build', '--quiet');
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_15/check/draft-true.html',
            __DIR__.'/_tmp/build_local/draft-true.html',
            '',
            true
        );

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_15/check/draft-false.html',
            __DIR__.'/_tmp/build_local/draft-false.html',
            '',
            true
        );
    }
}
