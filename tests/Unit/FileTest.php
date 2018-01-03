<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Tests\TestCase;

class FileTest extends TestCase
{
    function testFileGetUid()
    {
        $file = new File(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', '', ''));
        $this->assertNotEmpty($file->getUid());
    }
}
