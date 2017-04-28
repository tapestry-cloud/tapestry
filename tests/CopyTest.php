<?php

namespace Tapestry\Tests;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Cache;
use Tapestry\Entities\CacheStore;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\Project;
use Tapestry\Modules\Content\ReadCache;
use Tapestry\Modules\Content\WriteCache;

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
