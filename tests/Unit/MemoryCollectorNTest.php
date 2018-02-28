<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Modules\Collectors\Exclusions\DraftsExclusion;
use Tapestry\Modules\Collectors\Exclusions\PathExclusion;
use Tapestry\Modules\Collectors\MemoryCollector;
use Tapestry\Modules\Collectors\Mutators\FrontMatterMutator;
use Tapestry\Modules\Collectors\Mutators\IsScheduledMutator;
use Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Tests\TestCase;

class MemoryCollectorNTest extends TestCase
{

    public function testMemoryCollector()
    {
        try {
            $class = new MemoryCollector(
                [
                    [
                        'uid' => 'test-file_md',
                        'rawContent' => 'Hello World!',
                        'filename' => 'test-file.md',
                        'ext' => 'md',
                        'relativePath' => '_blog',
                        'relativePathname' => '_blog/test-file.md'
                    ],
                    [
                        'uid' => 'test-file-2_md',
                        'rawContent' => 'Hello World!',
                        'filename' => 'test-file-2.md',
                        'ext' => 'md',
                        'relativePath' => '_blog',
                        'relativePathname' => '_blog/test-file-2.md',
                        'data' => [
                            'draft' => true
                        ]
                    ]
                ],
                [
                    new SetDateDataFromFileNameMutator(),
                    new FrontMatterMutator(),
                    new IsScheduledMutator(),
                    new IsIgnoredMutator(['_views', '_templates'], ['_blog']),
                ],
                [
                    new DraftsExclusion(),
                    new PathExclusion('ignored_folder')
                ]
            );

            $arr = $class->collect();
            $this->assertTrue(is_array($arr));
            $this->assertCount(1, $arr);
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }
    }
}