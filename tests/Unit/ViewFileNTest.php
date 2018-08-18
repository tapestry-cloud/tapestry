<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Entities\ViewFile;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;

class ViewFileNTest extends TestCase
{

    public function testViewFile()
    {

        try{
            $memory = new MemorySource('template', '', 'template.phtml', 'phtml', '/', '/template.phtml');

            $viewFile = new ViewFile($memory);
            $this->assertSame($memory, $viewFile->getSource());
            $this->assertSame($memory->getData('uid'), $viewFile->getData('uid'));
            $this->assertSame($memory->getCompiledPermalink(), $viewFile->getPermalink());

            // @todo complete this for #320
            //$this->assertSame(url($memory->getPermalink()), $viewFile->getUrl());
            //$this->assertSame($memory->getData('date'), $viewFile->getDate());
            //$this->assertSame($memory->getData('categories', []), $viewFile->taxonomyList('categories'));

        } catch (\Exception $e) {
            $this->fail($e);
        }

    }

}
