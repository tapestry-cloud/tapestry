<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\Project;
use Tapestry\Entities\Renderers\HTMLRenderer;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockFile;

class RendererTest extends TestCase
{
    use MockFile;

    public function testHTMLRenderer()
    {
        $this->loadToTmp(__DIR__ . '/../assets/build_test_34/src');

        $project = new Project($this->tmpDirectory, $this->tmpDirectory . '/build_test', 'test');
        $renderer = new HTMLRenderer($project);

        $this->assertEquals(['htm', 'html'], $renderer->supportedExtensions());
        $this->assertTrue($renderer->canRender('html'));
        $this->assertTrue($renderer->canRender('htm'));
        $this->assertFalse($renderer->canRender('php'));
        $this->assertEquals('html', $renderer->getDestinationExtension('ext'));
        $this->assertTrue($renderer->supportsFrontMatter());

        $file = $this->mockFile($this->tmpDirectory . '/source/test.html');
        $this->assertEquals('Hello World', trim($renderer->render($file)));

        $renderer->mutateFile($file);
        $this->assertFalse($file->isRendered());
        $this->assertEquals('Hello World', $file->getData('content'));
        $this->assertEquals('phtml', $file->getExt());
    }
}
