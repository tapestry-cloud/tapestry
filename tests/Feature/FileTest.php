<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;

class FileTest extends CommandTestBase
{
    function testFileGetUid()
    {
        $file = new File(new SplFileInfo(__DIR__ . '/Mocks/TestFile.md', '', ''));
        $this->assertNotEmpty($file->getUid());
    }
}
