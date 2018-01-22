<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Steps\LexicalAnalysis;
use Tapestry\Steps\SyntaxAnalysis;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;

class ContentGraphNTest extends TestCase
{

    use MockTapestry;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testAnalysis()
    {
        $tapestry = $this->mockTapestry($this->tmpDirectory);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        $generator = new Generator([
            LexicalAnalysis::class,
            SyntaxAnalysis::class
        ], $tapestry);
        $generator->generate($project, new NullOutput());

        $f = 0;
    }
}