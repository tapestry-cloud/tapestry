<?php

namespace Tapestry\Tests;

use Carbon\Carbon;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Entities\WorkspaceScaffold;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Tapestry;
use Tapestry\Tests\Mocks\TestInvalidWorkspaceScaffoldStep;
use Tapestry\Tests\Mocks\TestWorkspaceScaffoldStep;

class WorkspaceScaffoldTest extends CommandTestBase
{
    public function testWorkspaceScaffoldClass()
    {
        $class = new WorkspaceScaffold('Test', 'A Description', [], []);
        $this->assertEquals('Test',$class->getName());
        $this->assertEquals('A Description', $class->getDescription());
        $this->assertEquals([], $class->getModel());
    }

    public function testExceptionThrownOnInvalidStepClass()
    {
        $class = new WorkspaceScaffold('Test', 'A Description', [new \stdClass()], []);
        $this->expectExceptionMessage('All workspace scaffold steps must be instances of \Tapestry\Entities\WorkspaceScaffold\Step.');
        $class->execute(new NullOutput());
    }

    public function testExceptionThrownOnStepClassInvalidReturn()
    {
        $class = new WorkspaceScaffold('Test', 'A Description', [new TestInvalidWorkspaceScaffoldStep()], []);
        $this->expectExceptionMessage('The result of your workspace scaffold step must be boolean.');
        $class->execute(new NullOutput());
    }

    public function testWorkspaceScaffoldStep()
    {
        $steps = [
            'begin' => new TestWorkspaceScaffoldStep()
        ];
        $class = new WorkspaceScaffold('Test', 'A Description', $steps, []);
        $this->assertTrue($class->execute(new NullOutput()));
        $this->assertEquals(['hello' => 'world'], $class->getModel());
    }
}
