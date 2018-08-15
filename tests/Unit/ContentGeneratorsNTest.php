<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\Output;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Entities\Project;
use Tapestry\Modules\Generators\ContentGeneratorFactory;
use Tapestry\Modules\Generators\Generator;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tapestry;
use Tapestry\Tests\TestCase;

class ContentGeneratorsNTest extends TestCase
{
    public function testContentGeneratorFactory()
    {
        $project = new Project('', '','');
        $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));
        $factory = new ContentGeneratorFactory([], $project);

        $this->assertFalse($factory->has('hello-world'));

        $factory->add(Generator::class);
        $this->assertTrue($factory->has('Generator'));

        $memory = new MemorySource('hello-world', '', 'index', 'phtml','/', '/index.phtml', []);

        $result = ($factory->get('Generator', $memory))->generate($project);
        $this->assertTrue(is_array($result));
        $this->assertSame($memory, $result[0]);

        // @todo finish this test
    }

    public function testGeneratorGenerator()
    {
        // Tapestry\Modules\Generators\Generator
        $this->assertTrue(false);
    }

    public function testCollectionItemGenerator()
    {
        $this->assertTrue(false);
    }

    public function testPaginationGenerator()
    {
        $this->assertTrue(false);
    }

    public function testTaxonomyArchiveGenerator()
    {
        $this->assertTrue(false);
    }

    public function testTaxonomyIndexGenerator()
    {
        $this->assertTrue(false);
    }
}