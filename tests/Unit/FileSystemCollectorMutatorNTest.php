<?php

namespace Tapestry\Tests\Unit;

use DateTime;
use Tapestry\Modules\Collectors\Mutators\FrontMatterMutator;
use Tapestry\Modules\Collectors\Mutators\IsDraftMutator;
use Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Tests\TestCase;

class FileSystemCollectorMutatorNTest extends TestCase
{

    public function testFrontMatterMutator()
    {
        $mutator = new FrontMatterMutator();
    }

    public function testIsDraftMutator()
    {
        $mutator = new IsDraftMutator();

        $file = $this->mockSplFileSource(__DIR__ . '/../Mocks/TestFile.md', 'Mocks', 'Mocks/TestFile.md');
        $mutator->mutate($file);

        // needs a file that is a draft

    }

    public function testisIgnoredMutator()
    {
        $mutator = new IsIgnoredMutator();
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