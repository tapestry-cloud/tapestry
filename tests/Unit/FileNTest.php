<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\ProjectFile;
use Tapestry\Tests\TestCase;

class FileNTest extends TestCase
{
    function testFileGetUid()
    {
        $file = new ProjectFile(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', '', ''));
        $this->assertNotEmpty($file->getUid());
    }
}
