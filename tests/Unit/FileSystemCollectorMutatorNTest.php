<?php

namespace Tapestry\Tests\Unit;

use DateTime;
use Tapestry\Modules\Collectors\Mutators\FrontMatterMutator;
use Tapestry\Modules\Collectors\Mutators\IsScheduledMutator;
use Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Tests\TestCase;

class FileSystemCollectorMutatorNTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public function testFrontMatterMutator()
    {
        $mutator = new FrontMatterMutator();

        $file = $this->mockSplFileSource(__DIR__ . '/../Mocks/TestFile.md', 'Mocks', 'Mocks/TestFile.md');
        $this->assertCount(1, $file->getData());

        $mutator->mutate($file);
        $this->assertCount(4, $file->getData());
        $this->assertSame('This is a test file...', trim($file->getRenderedContent()));
    }

    /**
     * @throws \Exception
     */
    public function testIsDraftMutator()
    {
        $mutator = new IsScheduledMutator(true,true);

        $file = $this->mockMemorySource('TestFile');
        $file->setData([
            'draft' => true,
            'date' => new DateTime('01-01-2015')
        ]);

        $mutator->mutate($file);
        $this->assertTrue($file->getData('draft'));

        // Scheduled Source that should be published (publish date in past, draft set to true)
        $mutator = new IsScheduledMutator(false,true);
        $mutator->mutate($file);
        $this->assertFalse($file->getData('draft'));

        // Scheduled Source that shouold not be published (publish date in future, draft set to true)
        $file = $this->mockMemorySource('TestFile');
        $file->setData([
            'draft' => true,
            'date' => new DateTime('01-01-2099')
        ]);

        $mutator->mutate($file);
        $this->assertTrue($file->getData('draft'));

        // Disabled
        $mutator = new IsScheduledMutator();
        $file = $this->mockMemorySource('TestFile');
        $file->setData([
            'draft' => true,
            'date' => new DateTime('01-01-2015')
        ]);
        $mutator->mutate($file);
        $this->assertTrue($file->getData('draft'));
    }

    public function testisIgnoredMutator()
    {
        $mutator = new IsIgnoredMutator(['_views', '_templates'], ['_blog']);

        $file = $this->mockMemorySource('test-file');
        $file->setOverloaded('relativePath', 'abc/123');
        $mutator->mutate($file);
        $this->assertFalse($file->isIgnored());

        $file = $this->mockMemorySource('test-file');
        $file->setOverloaded('relativePath', '_views/abc/123');
        $mutator->mutate($file);
        $this->assertTrue($file->isIgnored());

        $file = $this->mockMemorySource('test-file');
        $file->setOverloaded('relativePath', '_templates/abc/123');
        $mutator->mutate($file);
        $this->assertTrue($file->isIgnored());

        $file = $this->mockMemorySource('test-file');
        $file->setOverloaded('relativePath', '_blog/2017');
        $mutator->mutate($file);
        $this->assertFalse($file->isIgnored());

        $file = $this->mockMemorySource('test-file');
        $file->setOverloaded('relativePath', 'abc/123/_test');
        $mutator->mutate($file);
        $this->assertTrue($file->isIgnored());
    }

    public function testSetDateDataFromFileNameMutator()
    {
        $mutator = new SetDateDataFromFileNameMutator();

        $file = $this->mockSplFileSource(__DIR__ . '/../Mocks/TestFile.md', 'Mocks', 'Mocks/TestFile.md');
        $mutator->mutate($file);

        $this->assertCount(1, $file->getData());

        $file = $this->mockSplFileSource(__DIR__ . '/../Mocks/2018-02-01-this-is-a-test.md', 'Mocks', 'Mocks/2018-02-01-this-is-a-test.md');
        $mutator->mutate($file);

        $this->assertCount(4, $file->getData());
        $this->assertSame('this-is-a-test', $file->getData('slug'));
        $this->assertSame('This is a test', $file->getData('title'));
        $this->assertInstanceOf(DateTime::class, $file->getData('date'));
        $this->assertSame('2018-02-01', $file->getData('date')->format('Y-m-d'));

        $file = $this->mockSplFileSource(__DIR__ . '/../Mocks/01-02-2018-this-is-a-test.md', 'Mocks', 'Mocks/01-02-2018-this-is-a-test.md');
        $mutator->mutate($file);

        $this->assertCount(4, $file->getData());
        $this->assertSame('this-is-a-test', $file->getData('slug'));
        $this->assertSame('This is a test', $file->getData('title'));
        $this->assertInstanceOf(DateTime::class, $file->getData('date'));
        $this->assertSame('2018-02-01', $file->getData('date')->format('Y-m-d'));
    }
}