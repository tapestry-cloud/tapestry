<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Steps\BootKernel;
use Tapestry\Steps\LoadContentCollectors;
use Tapestry\Steps\LoadGraph;
use Tapestry\Steps\ParseContentTypes;
use Tapestry\Steps\LexicalAnalysis;
use Tapestry\Steps\LoadContentGenerators;
use Tapestry\Steps\LoadContentRenderers;
use Tapestry\Steps\LoadContentTypes;
use Tapestry\Steps\ReadCache;
use Tapestry\Steps\RunContentCollectors;
use Tapestry\Steps\RunGenerators;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;

class ContentGraphNTest extends TestCase
{

    use MockTapestry;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @todo this is not actually a unit test, its a feature test! MOVE IT
     */
    public function testAnalysis()
    {
        //$this->assertTrue(true); return;

        //$this->loadToTmp($this->assetPath('build_test_7/src'));
        $this->loadToTmp($this->assetPath('build_test_41/src'));
        $tapestry = $this->mockTapestry($this->tmpDirectory);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        $generator = new Generator([
            // Loading...
            BootKernel::class,
            ReadCache::class,
            LoadGraph::class,
            LoadContentTypes::class,
            LoadContentCollectors::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            // Collecting...
            RunContentCollectors::class,

            // Parsing/Lexical Analysis
            ParseContentTypes::class,
            LexicalAnalysis::class,

            // Generation/Compilation...
            RunGenerators::class,
            //SyntaxAnalysis::class,
            //RenderPlates::class

            // Shutdown...


        ], $tapestry);

        $this->markTestIncomplete('This test is a work in progress.');


        $this->assertEquals(0, $generator->generate($project, new NullOutput()));

        $this->assertTrue(true);
    }
}