<?php

namespace Tapestry\Tests\Feature;

use Tapestry\Entities\ProjectFile;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Console\DefaultInputDefinition;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\Filesystem\FileWriter;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Tapestry;
use Tapestry\Tests\TestCase;

class TaxonomyArchiveGeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $this->loadToTmp($this->assetPath('build_test_23/src'));

        // <Bootstrap Tapestry>
        $definitions = new DefaultInputDefinition();

        $tapestry = new Tapestry(new ArrayInput([
            '--site-dir' => $this->tmpPath(),
            '--env' => 'testing'
        ], $definitions));
        $generator = new Generator(config('steps', []), $tapestry);

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);
        $project->set('cmd_options', []);
        $generator->generate($project, new NullOutput);
        // </Bootstrap Tapestry>

        $this->assertTrue($project->has('compiled'));
        $this->assertInstanceOf(FlatCollection::class, $project->get('compiled'));

        /** @var FlatCollection $compiledFiles */
        $compiledFiles = $project->get('compiled');
        $this->assertEquals(7, $compiledFiles->count());
        $this->assertTrue(isset($compiledFiles['blog_categories_category_phtml_misc']));
        $this->assertInstanceOf(FileWriter::class, $compiledFiles['blog_categories_category_phtml_misc']);

        /** @var FileWriter $miscCategory */
        $miscCategory = $compiledFiles['blog_categories_category_phtml_misc'];
        $miscCategoryFile = $miscCategory->getFile();

        $this->assertInstanceOf(ProjectFile::class, $miscCategoryFile);
        $this->assertTrue($miscCategoryFile->hasData('blog_categories_items'));
        $this->assertTrue($miscCategoryFile->hasData('blog_categories'));

        $this->assertEquals(['misc', 'first-post'], $miscCategoryFile->getData('blog_categories', []));
        $this->assertEquals('misc', $miscCategoryFile->getData('taxonomyName', ''));

        /** @var FileWriter $index */
        $index = $compiledFiles['index_phtml'];
        $indexFile = $index->getFile();

        $this->assertTrue($indexFile->hasData('blog_categories_items'));
        $this->assertTrue($indexFile->hasData('blog_items'));
    }
}
