<?php

namespace Tapestry\Tests\Feature;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Modules\Content\LoadSourceFiles;
use Tapestry\Modules\ContentTypes\LoadContentTypes;
use Tapestry\Modules\Generators\LoadContentGenerators;
use Tapestry\Modules\Kernel\BootKernel;
use Tapestry\Modules\Renderers\LoadContentRenderers;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockTapestry;

class InternationalizationTest extends TestCase
{
    use MockTapestry;

    private function mockBuild()
    {
        $tapestry = $this->mockTapestry($this->tmpDirectory);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        $generator = new Generator([
            BootKernel::class,
            LoadContentTypes::class,
            LoadContentRenderers::class,
            LoadContentGenerators::class,
            LoadSourceFiles::class,
        ], $tapestry);
        $generator->generate($project, new NullOutput());

        return $project;
    }

    /**
     * Written for issue #256
     * @version 1.1.0
     * @link https://github.com/carbontwelve/tapestry/issues/256
     */
    public function testLanguageDefinedCorrectlyViaFrontMatter()
    {
        $this->loadToTmp(__DIR__ . '/../assets/build_test_1/src');
        $project = $this->mockBuild();

        /** @var FlatCollection $files */
        $files = $project->get('files');

        /** @var File $file */
        $file = $files->get('about_md');
        $this->assertEquals('en', $file->getData('language'));
    }

    /**
     * Written for issue #256
     * @version 1.1.0
     * @link https://github.com/carbontwelve/tapestry/issues/256
     */
    public function testLanguageDefinedCorrectlyViaPath()
    {
        $this->loadToTmp(__DIR__ . '/../assets/build_test_41/src');
        $project = $this->mockBuild();

        /** @var FlatCollection|File[] $files */
        $files = $project->get('files');

        $this->assertEquals('en', $files['_blog_en_2000-01-01-english-post_md']->getData('language'));
        $this->assertEquals('en', $files['_blog_fr_2000-01-02-english-override_md']->getData('language'));

        $this->assertEquals('fr', $files['_blog_fr_2000-01-01-french-post_md']->getData('language'));
        $this->assertEquals('fr', $files['_blog_en_2000-01-02-french-override_md']->getData('language'));
    }
}
