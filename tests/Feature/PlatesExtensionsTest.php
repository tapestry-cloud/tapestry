<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Tests\CommandTestBase;
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

        // Test with default Tapestry testing env set
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(__DIR__ . DIRECTORY_SEPARATOR . '_tmp'),
            __DIR__ . '/Mocks/TestEnvironmentFile.phtml', true
        );
        $this->assertEquals('env: testing', $viewFile->getContent());

        // Test with custom env set
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(
                __DIR__ . DIRECTORY_SEPARATOR . '_tmp',
                null,
                'abc123'
            ),
            __DIR__ . '/Mocks/TestEnvironmentFile.phtml', true
        );
        $this->assertEquals('env: abc123', $viewFile->getContent());
    }
}