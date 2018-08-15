<?php

namespace Tapestry\Tests\Unit;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Permalink;
use Tapestry\Modules\Source\SplFileSource;
use Tapestry\Tests\TestCase;

class SplFileSourceNTest extends TestCase
{

    /**
     * Test the SplFile implementation of the SourceInterface.
     *
     * @throws \Exception
     */
    public function testSplFileSource()
    {
        try {
            $class = new SplFileSource(new SplFileInfo(__DIR__ . '/../Mocks/TestFileNoBody.md', 'Mocks', 'Mocks/TestFileNoBody.md'));
            $this->assertSame('/Mocks/testfilenobody/index.md', $class->getCompiledPermalink());
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }

        $this->assertSame('md', $class->getExtension());
        $this->assertSame('TestFileNoBody.md', $class->getFilename());
        $this->assertSame('TestFileNoBody', $class->getBasename());
        $this->assertSame('Mocks_TestFileNoBody_md', $class->getUid());
        $this->assertFalse($class->hasData('hello-world'));
        $this->assertFalse($class->hasContent());
        $this->assertFalse($class->hasChanged());
        $this->assertFalse($class->isRendered());
        $this->assertFalse($class->isToCopy());
        $this->assertFalse($class->isIgnored());
        $this->assertInstanceOf(Permalink::class, $class->getPermalink());

        $class->setHasChanged();
        $this->assertTrue($class->hasChanged());

        $class->setRendered();
        $this->assertTrue($class->isRendered());

        $class->setToCopy();
        $this->assertTrue($class->isToCopy());

        $class->setIgnored();
        $this->assertTrue($class->isIgnored());

        $this->assertSame(['uid' => 'Mocks_TestFileNoBody_md'], $class->getData());

        try {
            $class->setDataFromArray([
                'a' => 123,
                'b' => 'abc',
                'c' => 3.14,

            ]);

            $class->setData('d', 'Hello World');
            $class->setData(['e' => 'elephant', 'f' => 'flamingo']);
        } catch (\Exception $e){
            $this->fail($e);
            return;
        }

        $this->assertSame(['uid' => 'Mocks_TestFileNoBody_md', 'a' => 123, 'b' => 'abc', 'c' => 3.14, 'd' => 'Hello World', 'e' => 'elephant', 'f' => 'flamingo'], $class->getData());

        try{
            $class->setData('date', '11th September 2001');
            $class->setData('permalink', '/abc/123/xyz.html');
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }

        /** @var \DateTime $date */
        $date = $class->getData('date');
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertSame('11-09-2001', $date->format('d-m-Y'));

        try {
            $this->assertSame('/abc/123/xyz.html', $class->getCompiledPermalink());
        }catch (\Exception $e) {
            $this->fail($e);
            return;
        }

        try {
            $class->setData('date', 'elephants');
        }catch (\Exception $e) {
            $this->assertSame('The date [elephants] is in a format not supported by Tapestry.', $e->getMessage());
        }

        $this->assertSame('11-09-2001', $date->format('d-m-Y'));

        //
        // Overloaded
        //

        $class->setOverloaded('ext', 'phtml');
        $this->assertSame('phtml', $class->getExtension());
        $this->assertSame('md', $class->getExtension(false));
        $this->assertSame('TestFileNoBody', $class->getBasename());
        $this->assertSame('TestFileNoBody', $class->getBasename(false));

        $class->setOverloaded('filename', 'hello-world.phtml');
        $this->assertSame('hello-world.phtml', $class->getFilename());
        $this->assertSame('TestFileNoBody.md', $class->getFilename(false));
        $this->assertSame('hello-world', $class->getBasename());
        $this->assertSame('TestFileNoBody', $class->getBasename(false));

        $class->setOverloaded('relativePath', 'abc/123');
        $this->assertSame('abc/123', $class->getRelativePath());
        $this->assertSame('Mocks', $class->getRelativePath(false));

        $class->setOverloaded('relativePathname', 'abc/123/hello-world.phtml');
        $this->assertSame('abc/123/hello-world.phtml', $class->getRelativePathname());
        $this->assertSame('Mocks/TestFileNoBody.md', $class->getRelativePathname(false));

        try{
            $this->assertSame(file_get_contents(__DIR__ . '/../Mocks/TestFileNoBody.md'), $class->getRawContent());
        } catch (\Exception $e) {
            $this->fail($e);
            return;
        }

        try{
            $class->getRenderedContent();
        } catch (\Exception $e) {
            $this->assertSame('The file [abc/123/hello-world.phtml] has not been loaded.', $e->getMessage());
        }

        $class->setRenderedContent('Hello World!');
        $this->assertSame('Hello World!', $class->getRenderedContent());
    }

}