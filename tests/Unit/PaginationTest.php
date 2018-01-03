<?php

namespace Tapestry\Tests\Unit;

use Tapestry\Tests\CommandTestBase;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Entities\Generators\PaginationGenerator;
use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Tests\TestCase;

class PaginationTest extends TestCase
{

    private function setupPagination(Project $project, $filePath)
    {
        $file = new File(new SplFileInfo($filePath, '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());

        $this->assertEquals(['PaginationGenerator'], $file->getData('generator'));

        $testItems = [];
        for ($i = 0; $i <= 99; $i++) {
            $testItems['item-' . $i] = 'item-' . $i;
            $project->set('files.item-' . $i, 'test-item-' . $i);
            $p = clone ($file);
            $p->setUid('item-' . $i);
            $project->set('compiled.item-' . $i, $p);
        }
        $file->setData(['test_items' => $testItems]);
        return new PaginationGenerator($file);
    }

    public function testPaginationCoreFunctionality()
    {
        $project = new Project('', '', 'test');
        $generator = $this->setupPagination($project, __DIR__ . '/../Mocks/TestPaginatorFile.phtml');

        $generatedFiles = $generator->generate($project);
        $this->assertTrue(is_array($generatedFiles));
        $this->assertEquals(17, count($generatedFiles));

        /**
         * @var File $firstPage
         * @var File $lastPage
         */
        $firstPage = array_shift($generatedFiles);
        $lastPage = array_pop($generatedFiles);

        /**
         * @var Pagination $firstPagePagination
         * @var Pagination $lastPagePagination
         */
        $firstPagePagination = $firstPage->getData('pagination');
        $lastPagePagination = $lastPage->getData('pagination');

        $this->assertInstanceOf(Pagination::class, $firstPagePagination);
        $this->assertInstanceOf(Pagination::class, $lastPagePagination);

        $this->assertEquals(6, count($firstPagePagination->getItems()));
        $this->assertEquals(4, count($lastPagePagination->getItems()));

        $this->assertEquals(['item-0', 'item-1', 'item-2', 'item-3', 'item-4', 'item-5'], array_keys($firstPagePagination->getItems()));
        $this->assertEquals(['item-96', 'item-97', 'item-98', 'item-99'], array_keys($lastPagePagination->getItems()));
    }

    /**
     * Written for issue #147
     * @link https://github.com/carbontwelve/tapestry/issues/147
     */
    public function testPaginationSkipFunctionality()
    {
        $project = new Project('', '', 'test');
        $generator = $this->setupPagination($project, __DIR__ . '/../Mocks/TestPaginatorSkipFile.phtml');

        $generatedFiles = $generator->generate($project);
        $this->assertTrue(is_array($generatedFiles));

        /** @var File $firstPage */
        $firstPage = $generatedFiles[0];

        /** @var Pagination $pagination */
        $pagination = $firstPage->getData('pagination');
        $this->assertInstanceOf(Pagination::class, $pagination);

        $items = $pagination->getItems();

        $this->assertEquals(6, count($items));
        $this->assertEquals(['item-6', 'item-7', 'item-8', 'item-9', 'item-10', 'item-11'], array_keys($items));
    }
}
