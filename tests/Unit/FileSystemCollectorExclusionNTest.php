<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Modules\Collectors\Exclusions\DraftsExclusion;
use Tapestry\Modules\Collectors\Exclusions\PathExclusion;
use Tapestry\Tests\TestCase;

class FileSystemCollectorExclusionNTest extends TestCase
{

    public function testDraftsExclusion()
    {
        $exclusion = new DraftsExclusion();
        $file = $this->mockMemorySource('not-a-draft');

        $this->assertFalse($exclusion->filter($file));

        $file->setData('draft', true);
        $this->assertTrue($exclusion->filter($file));

        $exclusion = new DraftsExclusion(true);
        $file = $this->mockMemorySource('not-a-draft');

        $this->assertFalse($exclusion->filter($file));

        $file->setData('draft', true);
        $this->assertFalse($exclusion->filter($file));
    }

    public function testPathExclusion()
    {
        $exclusion = new PathExclusion('_assets');

        $file = $this->mockMemorySource('test-assert', 'css');
        $file->setOverloaded('relativePath', '_assets/css');

        $this->assertTrue($exclusion->filter($file));

        $file->setOverloaded('relativePath', 'css/_assets');
        $this->assertTrue($exclusion->filter($file));

        $file->setOverloaded('relativePath', 'somewhere/else');
        $this->assertFalse($exclusion->filter($file));
    }
}