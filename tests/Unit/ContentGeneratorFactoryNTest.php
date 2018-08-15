<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Console\Output\Output;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Entities\Project;
use Tapestry\Modules\Generators\ContentGeneratorFactory;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tapestry;
use Tapestry\Tests\TestCase;

class ContentGeneratorFactoryNTest extends TestCase
{
    public function testContentGeneratorFactory()
    {
        $project = new Project('', '','');
        $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));
        $factory = new ContentGeneratorFactory([], $project);

        $this->assertFalse($factory->has('hello-world'));

        $factory->add(\Tapestry\Entities\Generators\CollectionItemGenerator::class);
        $this->assertTrue($factory->has('CollectionItemGenerator'));

        $memory = new MemorySource('hello-world', '', 'index', 'phtml','/');
    }
}
