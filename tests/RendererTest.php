<?php

namespace Tapestry\Tests;

use Tapestry\Entities\Project;
use Tapestry\Entities\Renderers\HTMLRenderer;
use Tapestry\Tests\Traits\MockFile;

class RendererTest extends CommandTestBase
{
    use MockFile;

    public function testHTMLRenderer()
    {
        $this->copyDirectory('/assets/build_test_34/src', '/_tmp');

        $project = new Project(__DIR__ . '/_tmp', __DIR__ . '/_tmp/build_test', 'test');
        $renderer = new HTMLRenderer($project);

        $this->assertEquals(['htm', 'html'], $renderer->supportedExtensions());
        $this->assertTrue($renderer->canRender('html'));
        $this->assertTrue($renderer->canRender('htm'));
        $this->assertFalse($renderer->canRender('php'));
        $this->assertEquals('html', $renderer->getDestinationExtension('ext'));
        $this->assertTrue($renderer->supportsFrontMatter());

        $file = $this->mockFile(__DIR__ . '/_tmp/source/test.html');
        $this->assertEquals('Hello World', trim($renderer->render($file)));

        $renderer->mutateFile($file);
        $this->assertFalse($file->isRendered());
        $this->assertEquals('Hello World', $file->getData('content'));
        $this->assertEquals('phtml', $file->getExt());
    }
}
