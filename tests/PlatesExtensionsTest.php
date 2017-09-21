<?php

namespace Tapestry\Tests;

use Tapestry\Tests\Traits\MockTapestry;
use Tapestry\Tests\Traits\MockViewFile;

class PlatesExtensionsTest extends CommandTestBase
{
    use MockViewFile;
    use MockTapestry;

    public function testEnvironmentExtension()
    {
        if (! file_exists(__DIR__ . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR . 'source')) {
            mkdir(__DIR__ . DIRECTORY_SEPARATOR . '_tmp' . DIRECTORY_SEPARATOR . 'source');
        }

        $viewFile = $this->mockViewFile($this->mockTapestry(__DIR__ . DIRECTORY_SEPARATOR . '_tmp'), __DIR__ . '/mocks/TestEnvironmentFile.phtml', true);
        $this->assertEquals('env: testing', $viewFile->getContent());

        $viewFile = $this->mockViewFile(
            $this->mockTapestry(
                __DIR__ . DIRECTORY_SEPARATOR . '_tmp',
                null,
                'abc123'
            ),
            __DIR__ . '/mocks/TestEnvironmentFile.phtml', true
        );
        $this->assertEquals('env: abc123', $viewFile->getContent());
    }
}