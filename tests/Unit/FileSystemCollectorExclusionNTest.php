<?php

namespace Tapestry\Tests\Unit;

use DateTime;
use Tapestry\Modules\Collectors\Exclusions\DraftsExclusion;
use Tapestry\Modules\Collectors\Mutators\FrontMatterMutator;
use Tapestry\Modules\Collectors\Mutators\IsDraftMutator;
use Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class FileSystemCollectorExclusionNTest extends TestCase
{

    public function testDraftsExclusion()
    {

        $exclusion = new DraftsExclusion();
        $file = $this->mockMemorySource('not-a-draft');

        $this->assertTrue($exclusion->filter($file));

        $file->setData('draft', true);
        $this->assertFalse($exclusion->filter($file));

        $exclusion = new DraftsExclusion(true);
        $file = $this->mockMemorySource('not-a-draft');

        $this->assertTrue($exclusion->filter($file));

        $file->setData('draft', true);
        $this->assertTrue($exclusion->filter($file));
    }

    public function testPathExclusion()
    {

    }
}