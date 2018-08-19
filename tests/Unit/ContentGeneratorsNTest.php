<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\DependencyGraph\Debug;
use Tapestry\Entities\DependencyGraph\Resolver;
use Tapestry\Entities\DependencyGraph\SimpleNode;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Modules\ContentTypes\ContentType;
use Tapestry\Modules\Generators\AbstractGenerator;
use Tapestry\Modules\Generators\CollectionItemGenerator;
use Tapestry\Modules\Generators\ContentGeneratorFactory;
use Tapestry\Modules\Generators\Generator;
use Tapestry\Modules\Generators\PaginationGenerator;
use Tapestry\Modules\Source\ClonedSource;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class ContentGeneratorsNTest extends TestCase
{
    // @todo add test to check this modifies the graph
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

        $this->markTestIncomplete('add test to check this modifies the graph');

    }

    // @todo add test to check this modifies the graph
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

        $this->markTestIncomplete('add test to check this modifies the graph');
    }

    public function testCollectionItemGenerator()
    {
        try {
            $project = new Project('', '', '');
            $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));

            $f = [];
            foreach(range('a','c') as $letter)
            {
                $tmp = new MemorySource('hello-world-'.$letter, '', 'index-'.$letter.'.phtml', 'phtml', '/', '/index-'.$letter.'.phtml', ['content_type' => 'mock', 'generator' => ['CollectionItemGenerator']]);
                $f[$tmp->getUid()] = $tmp;
                $project->getGraph()->addEdge('configuration', $tmp);
            } unset($letter);

            $mockContentType = $this->createMock(ContentType::class);
            $mockContentType->method('getSourceList')->withAnyParameters()->willReturn($f);
            $project->set('content_types.mock', $mockContentType);

            foreach (range('a','c') as $letter)
            {
                $source = $project->getSource('hello-world-'.$letter);

                $generator = new CollectionItemGenerator();
                $generator->setSource($source);

                $generated = $generator->generate($project);
                $generated = reset($generated);
                $this->assertSame($source, $generated);

                $this->assertEquals([], $source->getData('generator'));
                $this->assertTrue(!is_null($source->getData('previous_next')));

                /** @var Pagination $pagination */
                $pagination = $source->getData('previous_next');
                $this->assertInstanceOf(Pagination::class, $pagination);

                $previous = $pagination->getPrevious();
                $next = $pagination->getNext();

                if ($letter === 'a') {
                    $this->assertNull($previous);
                    $this->assertSame($project->getSource('hello-world-b'), $next->getSource());
                }

                if ($letter === 'b') {
                    $this->assertSame($project->getSource('hello-world-a'), $previous->getSource());
                    $this->assertSame($project->getSource('hello-world-c'), $next->getSource());
                }

                if ($letter === 'c') {
                    $this->assertSame($project->getSource('hello-world-b'), $previous->getSource());
                    $this->assertNull($next);
                }
            } unset($letter);

            //
            // Check Graph is updated with correct dependencies
            //

            $dep = (new Resolver())->resolve($project->getSource('hello-world-a'));
            $this->assertCount(4, $dep);

            $dep = (new Resolver())->resolve($project->getSource('hello-world-b'));
            $this->assertCount(6, $dep);

            $dep = (new Resolver())->resolve($project->getSource('hello-world-c'));
            $this->assertCount(4, $dep);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    // @todo add test to check this modifies the graph
    public function testPaginationGenerator()
    {
        try {
            $project = new Project('', '', '');
            $project->getGraph()->setRoot(new SimpleNode('configuration', 'hello world'));

            $files = [];

            foreach (range('a','z') as $l){
                $f = new MemorySource('hello-world-'.$l, '', 'index-'.$l.'.phtml', 'phtml', '/', '/index-'.$l.'.phtml');
                $files[$f->getUid()] = $f;
                $project->getGraph()->addEdge('configuration', $f);
            } unset($f);

            $t = new MemorySource('template', '', 'template.phtml', 'phtml', '/', '/template.phtml', ['mock_items' => $files, 'generator' => ['PaginationGenerator'], 'pagination' => ['provider' => 'mock']]);

            $project->getGraph()->addEdge('configuration', $t);

            $generator = new PaginationGenerator();
            $generator->setSource($t);

            $result = $generator->generate($project);

            $this->assertTrue(is_array($result));
            $this->assertCount(6, $result);

            foreach ($result as $k => $tmp) {
                $this->assertInstanceOf(ClonedSource::class, $tmp);

                if ($k === 0) {
                    $this->assertEquals('template_page_1', $tmp->getUid());
                    $this->assertEquals('/template/index.phtml', $tmp->getCompiledPermalink());

                    /** @var Pagination $pagination */
                    $pagination = $tmp->getData('pagination');
                    $this->assertNull($pagination->getPrevious());
                    $this->assertEquals('template_page_2', $pagination->getNext()->getSource()->getUid());
                } else {
                    $this->assertEquals('template_page_' . ($k+1), $tmp->getUid());
                    $this->assertEquals('/template/'. ($k+1) .'/index.phtml', $tmp->getCompiledPermalink());
                }
            } unset($tmp);

            //
            // Check Graph is updated with correct dependencies
            //

            $debug = new Debug($project->getGraph());
            $x = $debug->graphViz('configuration');

            $x = 1;

        } catch (\Exception $e) {
            $this->fail($e);
        }
        //$this->markTestIncomplete('add test to check this modifies the graph');
    }

    // @todo add test to check this modifies the graph
    public function testTaxonomyArchiveGenerator()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }

    // @todo add test to check this modifies the graph
    public function testTaxonomyIndexGenerator()
    {
        $this->markTestIncomplete('This test has not been implemented yet');
    }
}
