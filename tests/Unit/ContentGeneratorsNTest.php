<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentType;
use Tapestry\Modules\Generators\AbstractGenerator;
use Tapestry\Modules\Generators\CollectionItemGenerator;
use Tapestry\Modules\Generators\ContentGeneratorFactory;
use Tapestry\Modules\Generators\Generator;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class ContentGeneratorsNTest extends TestCase
{
    public function testContentGeneratorFactory()
    {
        try {
            $project = new Project('', '', '');
            $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));
            $factory = new ContentGeneratorFactory([], $project);

            $this->assertFalse($factory->has('hello-world'));

            $factory->add(Generator::class);
            $this->assertTrue($factory->has('Generator'));

            $memory = new MemorySource('hello-world', '', 'index', 'phtml', '/', '/index.phtml', []);

            $result = ($factory->get('Generator', $memory))->generate($project);
            $this->assertTrue(is_array($result));
            $this->assertSame($memory, $result[0]);

            // @todo finish this test
        } catch (\Exception $e) {
            $this->fail($e);
        }

    }

    public function testGeneratorGenerator()
    {
        try {
            $project = new Project('', '', '');
            $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));

            $a = new MemorySource('hello-world-a', '', 'index-a', 'phtml', '/', '/index-a.phtml', []);
            $b = new MemorySource('hello-world-b', '', 'index-b', 'phtml', '/', '/index-b.phtml', ['generator' => ['mock']]);

            $generatorMock = $this->createMock(AbstractGenerator::class);
            $generatorMock->method('generate')->with($project)->willReturn([$a]);

            $factoryMock = $this->createMock(ContentGeneratorFactory::class);
            $factoryMock->method('get')->with('mock', $b)->willReturn($generatorMock);
            $project['content_generators'] = $factoryMock;

            $generator = new Generator();
            $generator->setSource($b);

            $generated = $generator->generate($project);
            $this->assertSame($a, reset($generated));
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testCollectionItemGenerator()
    {
        try {
            $project = new Project('', '', '');
            $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));

            $a = new MemorySource('hello-world-a', '', 'index-a', 'phtml', '/', '/index-a.phtml', ['content_type' => 'mock']);
            $b = new MemorySource('hello-world-b', '', 'index-b', 'phtml', '/', '/index-b.phtml', ['content_type' => 'mock', 'generator' => ['CollectionItemGenerator']]);
            $c = new MemorySource('hello-world-c', '', 'index-c', 'phtml', '/', '/index-c.phtml', ['content_type' => 'mock']);

            $f = [
                $b->getUid() => $b, $a->getUid() => $a, $c->getUid() => $c
            ];

            $mockContentType = $this->createMock(ContentType::class);
            $mockContentType->method('getSourceList')->withAnyParameters()->willReturn($f);
            $project->set('content_types.mock', $mockContentType);
            $project->set('compiled', $f);

            $generator = new CollectionItemGenerator();
            $generator->setSource($a);

            $generated = $generator->generate($project);
            $generated = reset($generated);
            $this->assertSame($a, $generated);

            $this->assertEquals([], $a->getData('generator'));
            $this->assertTrue(!is_null($a->getData('previous_next')));

            /** @var Pagination $pagination */
            $pagination = $a->getData('previous_next');
            $this->assertInstanceOf(Pagination::class, $pagination);

            $previous = $pagination->getPrevious();
            $next = $pagination->getNext();

            $n = 1;

        } catch (\Exception $e) {
            $this->fail($e);
        }

        //$this->markTestIncomplete('This test has not been implemented yet');
    }

    public function testPaginationGenerator()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }

    public function testTaxonomyArchiveGenerator()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }

    public function testTaxonomyIndexGenerator()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }
}
