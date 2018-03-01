<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Project;
use Tapestry\Generator;
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
            ReadCache::class,
            LoadContentTypes::class,
            LoadContentCollectors::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            RunContentCollectors::class,
            //LoadSourceFileTree::class,


            ParseContentTypes::class,

            //SyntaxAnalysis::class,
            LexicalAnalysis::class,
            RenderPlates::class
        ], $tapestry);

        $generator->generate($project, new NullOutput());

        $this->assertTrue(true);

        // @todo check graph is built
        // Once run through, touch some of the files and then repeat to check that only files
        // related to the ones "changed" are marked for rendering.
        $n = 1;

        //touch($this->tmpDirectory . '/source/something.html');

        //$generator->generate($project, new NullOutput());

        //
        // For refactoring issues #300, #297, #284, #282, #270:
        //
        // Tapestry now parses all files in the source folder and builds an hash table containing them all
        // and their last change date.
        //
        // Test that the following happens:
        //
        // [ ] All files in source folder are loaded
        // [ ] All files in source folder are bucketed correctly
        //
        // Because ignored files are still "parsed" during the LoadSourceFileTree step it also needs to be checked that
        // they have an ignored flag set. This ensures that template files don't then get copied from source to dist.
        //
        // [ ] Ignored files are ignored
        //

        $f = 0;
    }
}