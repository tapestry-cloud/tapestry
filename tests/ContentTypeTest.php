<?php

namespace Tapestry\Tests;

class ContentTypeTest extends CommandTestBase
{
    public function testContentTypeTaxonomyDefaultsSetOnFiles()
    {
        $this->copyDirectory('assets/build_test_16/src', '_tmp');
        $output = $this->runCommand('build', ['--quiet']);
        $this->assertEquals(0, $output->getStatusCode());

        $this->assertFileEquals(
            __DIR__.'/assets/build_test_16/check/index.html',
            __DIR__.'/_tmp/build_local/index.html',
            '',
            true
        );
    }
}
