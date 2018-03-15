<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Steps\BootKernel;
use Tapestry\Steps\LoadAST;
use Tapestry\Steps\LoadContentCollectors;
use Tapestry\Steps\ParseContentTypes;
use Tapestry\Steps\LexicalAnalysis;
use Tapestry\Steps\LoadContentGenerators;
use Tapestry\Steps\LoadContentRenderers;
use Tapestry\Steps\LoadContentTypes;
use Tapestry\Steps\LoadSourceFileTree;
use Tapestry\Steps\ReadCache;
use Tapestry\Steps\RenderPlates;
use Tapestry\Steps\RunContentCollectors;
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
        //$this->assertTrue(true); return;

        //$this->loadToTmp($this->assetPath('build_test_7/src'));
        $this->loadToTmp($this->assetPath('build_test_41/src'));
        $tapestry = $this->mockTapestry($this->tmpDirectory);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        $generator = new Generator([
            BootKernel::class,
            ReadCache::class,
            LoadAST::class,
            LoadContentTypes::class,
            LoadContentCollectors::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            RunContentCollectors::class,

            ParseContentTypes::class,

            //SyntaxAnalysis::class,
            //LexicalAnalysis::class,
            //RenderPlates::class
        ], $tapestry);

        $generator->generate($project, new NullOutput());

        $this->assertTrue(true);
    }
}