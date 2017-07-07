<?php

namespace Tapestry\Tests;

use Carbon\Carbon;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\File;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Entities\ViewFile;
use Tapestry\Entities\WorkspaceScaffold;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Tapestry;

class WorkspaceScaffoldTest extends CommandTestBase
{
    public function testWorkspaceScaffoldClass()
    {
        $class = new WorkspaceScaffold('Test', 'A Description', [], [], []);
        $this->assertEquals('Test',$class->getName());
        $this->assertEquals('A Description', $class->getDescription());
        $this->assertEquals([], $class->getModel());
    }

    public function testWorkspaceScaffoldStep()
    {
        // @todo
    }
}
