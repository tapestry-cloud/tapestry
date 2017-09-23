<?php

namespace Tapestry\Tests;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Console\Application;
use Tapestry\Entities\WorkspaceScaffold;
use Tapestry\Tests\Mocks\TestInvalidWorkspaceScaffoldStep;
use Tapestry\Tests\Mocks\TestWorkspaceScaffoldStep;
use Tapestry\Tests\Traits\MockTapestry;

class WorkspaceScaffoldTest extends CommandTestBase
{
    use MockTapestry;

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
        $this->assertTrue($class->isComplete());
    }

    public function testMakeCommandExists()
    {
        $tapestry = $this->mockTapestry();
        /** @var Application $application */
        $application = $tapestry->getContainer()->get(Application::class);
        $this->assertTrue($application->has('make'));
    }
}
