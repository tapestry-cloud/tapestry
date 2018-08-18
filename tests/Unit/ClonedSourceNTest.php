<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Modules\Source\ClonedSource;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class ClonedSourceNTest extends TestCase
{

    /**
     * Test that ClonedSource does what it says on the tin.
     */
    public function testClonedSource()
    {
        try {
            $memory = new MemorySource('memory_123', 'Howdy!', 'memory.md', 'md', 'memory/123', 'memory/123/memory.md');
            $clone = new ClonedSource($memory);

            $this->assertNotSame($memory, $clone);
            $this->assertEquals($memory->getUid(), $clone->getUid());
            $this->assertEquals($memory->getRawContent(), $clone->getRawContent());
            $this->assertEquals($memory->getFilename(), $clone->getFilename());
            $this->assertEquals($memory->getExtension(), $clone->getExtension());
            $this->assertEquals($memory->getRelativePath(), $clone->getRelativePath());
            $this->assertEquals($memory->getRelativePathname(), $clone->getRelativePathname());
            $this->assertEquals($memory->getData(), $clone->getData());

            $this->assertFalse($memory->isClone());
            $this->assertTrue($clone->isClone());

        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }

}