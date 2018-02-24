<?php

namespace Tapestry\Tests\Unit;

use DateTime;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Modules\Source\SplFileSource;
use Tapestry\Tests\TestCase;

class FileSystemCollectorMutatorNTest extends TestCase
{

    public function testFrontMatterMutator()
    {
        // @todo
    }

    public function testIsDraftMutator()
    {
        // @todo
    }

    public function testisIgnoredMutator()
    {
        // @todo
    }

    public function testSetDateDataFromFileNameMutator()
    {
        try {
            $file = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/TestFile.md', 'Mocks', 'Mocks/TestFile.md'));
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $mutator = new SetDateDataFromFileNameMutator();
        $mutator->mutate($file);
        $this->assertCount(1, $file->getData());

        try {
            $file = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/2018-02-01-this-is-a-test.md', 'Mocks', 'Mocks/2018-02-01-this-is-a-test.md'));
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $mutator->mutate($file);
        $this->assertCount(4, $file->getData());
        $this->assertSame('this-is-a-test', $file->getData('slug'));
        $this->assertSame('This is a test', $file->getData('title'));
        $this->assertInstanceOf(DateTime::class, $file->getData('date'));
        $this->assertSame('2018-02-01', $file->getData('date')->format('Y-m-d'));

        try {
            $file = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/01-02-2018-this-is-a-test.md', 'Mocks', 'Mocks/01-02-2018-this-is-a-test.md'));
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }

        $mutator->mutate($file);
        $this->assertCount(4, $file->getData());
        $this->assertSame('this-is-a-test', $file->getData('slug'));
        $this->assertSame('This is a test', $file->getData('title'));
        $this->assertInstanceOf(DateTime::class, $file->getData('date'));
        $this->assertSame('2018-02-01', $file->getData('date')->format('Y-m-d'));

        // @todo
    }
}