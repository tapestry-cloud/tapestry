<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Tests\CommandTestBase;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;
use Tapestry\Tests\Traits\MockViewFile;

class PlatesExtensionsTest extends TestCase
{
    use MockViewFile;
    use MockTapestry;

    public function testEnvironmentExtension()
    {

        $this->copy(__DIR__ . '/../Mocks/TestEnvironmentFile.phtml', $this->tmpDirectory . '/source/TestEnvironmentFile.phtml');

        // Test with default Tapestry testing env set
        $viewFile = $this->mockViewFile(
            $this->mockTapestry($this->tmpDirectory),
            $this->tmpDirectory . '/source/TestEnvironmentFile.phtml', true
        );
        $this->assertEquals('env: testing', $viewFile->getContent());

        // Test with custom env set
        $viewFile = $this->mockViewFile(
            $this->mockTapestry(
                $this->tmpDirectory,
                null,
                'abc123'
            ),
            $this->tmpDirectory . '/source/TestEnvironmentFile.phtml', true
        );
        $this->assertEquals('env: abc123', $viewFile->getContent());
    }
}