<?php

namespace Tapestry\Tests;

use Symfony\Component\Console\Output\NullOutput;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\File;
use Tapestry\Entities\Filesystem\FileWriter;
use Tapestry\Entities\Project;
use Tapestry\Generator;
use Tapestry\Tapestry;

class TaxonomyArchiveGeneratorTest extends CommandTestBase
{
    public function testGenerator()
    {
        $this->copyDirectory('assets/build_test_23/src', '_tmp');

        $tapestry = new Tapestry();
        $generator = new Generator($tapestry->getContainer()->get('Compile.Steps'), $tapestry);
        $project = new Project(__DIR__ . DIRECTORY_SEPARATOR . '_tmp', 'testing');

        $project->set('cmd_options', []);

        $tapestry->getContainer()->add(Project::class, $project);
        $generator->generate($project, new NullOutput);

        $this->assertTrue($project->has('compiled'));
        $this->assertInstanceOf(FlatCollection::class, $project->get('compiled'));

        /** @var FlatCollection $compiledFiles */
        $compiledFiles = $project->get('compiled');
        $this->assertEquals(6, $compiledFiles->count());
        $this->assertTrue(isset($compiledFiles['blog_categories_category_phtml_misc']));
        $this->assertInstanceOf(FileWriter::class, $compiledFiles['blog_categories_category_phtml_misc']);

        /** @var FileWriter $miscCategory */
        $miscCategory = $compiledFiles['blog_categories_category_phtml_misc'];
        $miscCategoryFile = $miscCategory->getFile();

        $this->assertInstanceOf(File::class, $miscCategoryFile);
        $this->assertTrue($miscCategoryFile->hasData('blog_categories_items'));

        $n = 1;
    }
}
