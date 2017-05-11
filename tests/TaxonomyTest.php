<?php

namespace Tapestry\Tests;

use PHPUnit_Framework_Constraint_IsEqual;
use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Entities\Taxonomy;
use Tapestry\Modules\Content\FrontMatter;

class TaxonomyTest extends CommandTestBase
{
    /**
     * Return a relative path to a file or directory using base directory.
     * When you set $base to /website and $path to /website/store/library.php
     * this function will return /store/library.php
     *
     * @param   String   $base   A base path used to construct relative path. For example /website
     * @param   String   $path   A full path to file or directory used to construct relative path. For example /website/store/library.php
     *
     * @return  String
     */
    private function getRelativePath($base, $path) {
        // On windows strip drive letter
        $base = preg_replace('/^[A-Z]:/i', '', $base);
        $path = preg_replace('/^[A-Z]:/i', '', $path);

        // Normalise separator
        $base = str_replace(['/', '\\'], '/', $base);
        $path = str_replace(['/', '\\'], '/', $path);
        $separator = '/';

        $base = array_slice(explode($separator, rtrim($base,$separator)),1);
        $path = array_slice(explode($separator, rtrim($path,$separator)),1);

        return $separator.implode($separator, array_slice($path, count($base)));
    }

    private function mockFile($filePath)
    {
        $file = new File(new SplFileInfo($filePath, $this->getRelativePath(__DIR__, $filePath), $this->getRelativePath(__DIR__, $filePath)));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());
        $file->getUid(); // Force the file to generate its uid
        return $file;
    }

    /**
     * Written for issue #180
     * @link https://github.com/carbontwelve/tapestry/issues/180
     */
    public function testTaxonomyClassNameCapitalisation()
    {
        // Taxonomy should normalise its name to lower case
        $taxonomy = new Taxonomy('Test');
        $this->assertEquals('test', $taxonomy->getName());

        $taxonomy = new Taxonomy('TEST');
        $this->assertEquals('test', $taxonomy->getName());

        $taxonomy = new Taxonomy('test');
        $this->assertEquals('test', $taxonomy->getName());
    }

    /**
     * Written for issue #180
     * @link https://github.com/carbontwelve/tapestry/issues/180
     */
    public function testTaxonomyNameCharacters()
    {
        // Taxonomy should normalise name by filtering out spaces
        $taxonomy = new Taxonomy('t e s t');
        $this->assertEquals('t-e-s-t', $taxonomy->getName());

        $taxonomy = new Taxonomy('t-e s-t');
        $this->assertEquals('t-e-s-t', $taxonomy->getName());

        $taxonomy = new Taxonomy('t-e  s-t');
        $this->assertEquals('t-e-s-t', $taxonomy->getName());

        $taxonomy = new Taxonomy('test 123');
        $this->assertEquals('test-123', $taxonomy->getName());

        $taxonomy = new Taxonomy('test 123 ');
        $this->assertEquals('test-123', $taxonomy->getName());

        $taxonomy = new Taxonomy(' test 123 ');
        $this->assertEquals('test-123', $taxonomy->getName());

        $taxonomy = new Taxonomy(' test  123 ');
        $this->assertEquals('test-123', $taxonomy->getName());
    }

    /**
     * Written for issue #180, #182
     * @link https://github.com/carbontwelve/tapestry/issues/180
     * @link https://github.com/carbontwelve/tapestry/issues/182
     */
    public function testTaxonomyClassClassificationCapitalisation()
    {
        // Taxonomy should normalise classifications to lower case
        $taxonomy = new Taxonomy('test');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-01-a.md'), 'Classification');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-02-b.md'), 'classification');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-03-c.md'), 'CLASSIFICATION');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-04-d.md'), ' CLASSIFICATION');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-05-e.md'), 'ClassificatioN ');

        $this->assertEquals(['classification'], array_keys($taxonomy->getFileList()));
    }

    /**
     * Written for issue #180, #182
     * @link https://github.com/carbontwelve/tapestry/issues/180
     * @link https://github.com/carbontwelve/tapestry/issues/182
     */
    public function testTaxonomyClassClassificationCharacters()
    {
        // Taxonomy should normalise classifications by filtering out spaces
        $taxonomy = new Taxonomy('test');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-01-a.md'), 'Classification 123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-02-b.md'), 'classification-123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-03-c.md'), 'CLASSIFICATION  123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-04-d.md'), '  CLASSIFICATION 123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-05-e.md'), 'ClassificatioN 123 ');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-05-f.md'), '  ClassificatioN 123 ');

        $this->assertEquals(['classification-123'], array_keys($taxonomy->getFileList()));
    }

    /**
     * Written for issue #180
     * @link https://github.com/carbontwelve/tapestry/issues/180
     * Note: When you run getFileList on the same instance of Taxonomy twice it will order over the previous order.
     *       this is not a problem when each item has a different timestamp, however when two items have the same they
     *       will swap positions when the same getFileList method is called twice.
     *
     *       Also between different versions of PHP, and even on different systems the ordering of two items that have
     *       the same timestamp ends up being random, sometimes F will be before E and sometimes E will be before F.
     *       So that this works consistantly this test uses the isOr method to check the output is correct.
     */
    public function testTaxonomyClassOrder()
    {
        $descArrayA = [
            '_mocks_TaxonomyMocks_2016-01-05-e_md',
            '_mocks_TaxonomyMocks_2016-01-05-f_md',
            '_mocks_TaxonomyMocks_2016-01-04-d_md',
            '_mocks_TaxonomyMocks_2016-01-03-c_md',
            '_mocks_TaxonomyMocks_2016-01-02-b_md',
            '_mocks_TaxonomyMocks_2016-01-01-a_md',
        ];
        $descArrayB = [
            '_mocks_TaxonomyMocks_2016-01-05-f_md',
            '_mocks_TaxonomyMocks_2016-01-05-e_md',
            '_mocks_TaxonomyMocks_2016-01-04-d_md',
            '_mocks_TaxonomyMocks_2016-01-03-c_md',
            '_mocks_TaxonomyMocks_2016-01-02-b_md',
            '_mocks_TaxonomyMocks_2016-01-01-a_md',
        ];
        $ascArrayA = [
            '_mocks_TaxonomyMocks_2016-01-01-a_md',
            '_mocks_TaxonomyMocks_2016-01-02-b_md',
            '_mocks_TaxonomyMocks_2016-01-03-c_md',
            '_mocks_TaxonomyMocks_2016-01-04-d_md',
            '_mocks_TaxonomyMocks_2016-01-05-f_md',
            '_mocks_TaxonomyMocks_2016-01-05-e_md',
        ];
        $ascArrayB = [
            '_mocks_TaxonomyMocks_2016-01-01-a_md',
            '_mocks_TaxonomyMocks_2016-01-02-b_md',
            '_mocks_TaxonomyMocks_2016-01-03-c_md',
            '_mocks_TaxonomyMocks_2016-01-04-d_md',
            '_mocks_TaxonomyMocks_2016-01-05-e_md',
            '_mocks_TaxonomyMocks_2016-01-05-f_md',
        ];

        $taxonomy = new Taxonomy('test');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-01-a.md'), 'Classification 123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-02-b.md'), 'classification-123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-03-c.md'), 'CLASSIFICATION  123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-04-d.md'), '  CLASSIFICATION 123');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-05-e.md'), 'ClassificatioN 123 ');
        $taxonomy->addFile($this->mockFile(__DIR__ . '/mocks/TaxonomyMocks/2016-01-05-f.md'), '   ClassificatioN 123 ');

        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList()['classification-123']), $descArrayA, $descArrayA));
        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList('DESC')['classification-123']), $descArrayA, $descArrayB));
        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList('ASC')['classification-123']), $ascArrayA, $ascArrayB));
    }

    private function isOr($test, $checkA, $checkB) {

        $constraintA = new PHPUnit_Framework_Constraint_IsEqual(
            $checkA,
            0.0,
            10,
            false,
            false
        );

        if ($constraintA->evaluate($test, '', true) === true){
            return true;
        }

        $constraintB = new PHPUnit_Framework_Constraint_IsEqual(
            $checkB,
            0.0,
            10,
            false,
            false
        );

        if ($constraintB->evaluate($test, '', true) === true){
            return true;
        }

        return false;
    }
}
