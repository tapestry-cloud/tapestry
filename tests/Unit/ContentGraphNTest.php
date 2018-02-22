<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Steps\LexicalAnalysis;
use Tapestry\Steps\LoadContentGenerators;
use Tapestry\Steps\LoadContentRenderers;
use Tapestry\Steps\LoadContentTypes;
use Tapestry\Steps\LoadSourceFileTree;
use Tapestry\Steps\ReadCache;
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
        $this->loadToTmp($this->assetPath('build_test_7/src'));
        $tapestry = $this->mockTapestry($this->tmpDirectory);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        $generator = new Generator([
            ReadCache::class,
            LoadContentTypes::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            LoadSourceFileTree::class,

            //SyntaxAnalysis::class,
            //LexicalAnalysis::class,
        ], $tapestry);
        $generator->generate($project, new NullOutput());

        touch($this->tmpDirectory . '/source/something.html');

        $generator->generate($project, new NullOutput());

        $f = 0;
    }
}