<?php

namespace Tapestry\Tests\Unit;

use PHPUnit\Framework\Constraint\IsEqual;
use Tapestry\Entities\Taxonomy;
use Tapestry\Modules\Source\MemorySource;
use Tapestry\Tests\TestCase;
use Tapestry\Tests\Traits\MockFile;

class TaxonomyNTest extends TestCase
{
    use MockFile;

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
     * Written for issue #180, #182, #322
     * @link https://github.com/carbontwelve/tapestry/issues/180
     * @link https://github.com/carbontwelve/tapestry/issues/182
     * @link https://github.com/carbontwelve/tapestry/issues/322
     * @throws \Exception
     */
    public function testTaxonomyClassClassificationCapitalisation()
    {
        // Taxonomy should normalise classifications to lower case
        $taxonomy = new Taxonomy('test');

        $taxonomy->addFile(new MemorySource('hello-world-a', '', '2016-01-01-a.md', 'md', '_blog', '_blog/2016-01-01-a.md'), 'Classification');
        $taxonomy->addFile(new MemorySource('hello-world-a', '', '2016-01-01-a.md', 'md', '_blog', '_blog/2016-01-01-a.md'), 'classification');
        $taxonomy->addFile(new MemorySource('hello-world-a', '', '2016-01-01-a.md', 'md', '_blog', '_blog/2016-01-01-a.md'), 'CLASSIFICATION');
        $taxonomy->addFile(new MemorySource('hello-world-a', '', '2016-01-01-a.md', 'md', '_blog', '_blog/2016-01-01-a.md'), ' CLASSIFICATION');
        $taxonomy->addFile(new MemorySource('hello-world-a', '', '2016-01-01-a.md', 'md', '_blog', '_blog/2016-01-01-a.md'), 'ClassificatioN ');

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
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-01-a.md'), realpath(__DIR__ . '/../')), 'Classification 123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-02-b.md'), realpath(__DIR__ . '/../')), 'classification-123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-03-c.md'), realpath(__DIR__ . '/../')), 'CLASSIFICATION  123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-04-d.md'), realpath(__DIR__ . '/../')), '  CLASSIFICATION 123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-05-e.md'), realpath(__DIR__ . '/../')), 'ClassificatioN 123 ');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-05-f.md'), realpath(__DIR__ . '/../')), '  ClassificatioN 123 ');

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
            '_Mocks_TaxonomyMocks_2016-01-05-e_md',
            '_Mocks_TaxonomyMocks_2016-01-05-f_md',
            '_Mocks_TaxonomyMocks_2016-01-04-d_md',
            '_Mocks_TaxonomyMocks_2016-01-03-c_md',
            '_Mocks_TaxonomyMocks_2016-01-02-b_md',
            '_Mocks_TaxonomyMocks_2016-01-01-a_md',
        ];
        $descArrayB = [
            '_Mocks_TaxonomyMocks_2016-01-05-f_md',
            '_Mocks_TaxonomyMocks_2016-01-05-e_md',
            '_Mocks_TaxonomyMocks_2016-01-04-d_md',
            '_Mocks_TaxonomyMocks_2016-01-03-c_md',
            '_Mocks_TaxonomyMocks_2016-01-02-b_md',
            '_Mocks_TaxonomyMocks_2016-01-01-a_md',
        ];
        $ascArrayA = [
            '_Mocks_TaxonomyMocks_2016-01-01-a_md',
            '_Mocks_TaxonomyMocks_2016-01-02-b_md',
            '_Mocks_TaxonomyMocks_2016-01-03-c_md',
            '_Mocks_TaxonomyMocks_2016-01-04-d_md',
            '_Mocks_TaxonomyMocks_2016-01-05-f_md',
            '_Mocks_TaxonomyMocks_2016-01-05-e_md',
        ];
        $ascArrayB = [
            '_Mocks_TaxonomyMocks_2016-01-01-a_md',
            '_Mocks_TaxonomyMocks_2016-01-02-b_md',
            '_Mocks_TaxonomyMocks_2016-01-03-c_md',
            '_Mocks_TaxonomyMocks_2016-01-04-d_md',
            '_Mocks_TaxonomyMocks_2016-01-05-e_md',
            '_Mocks_TaxonomyMocks_2016-01-05-f_md',
        ];

        $taxonomy = new Taxonomy('test');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-01-a.md'), realpath(__DIR__ . '/../')), 'Classification 123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-02-b.md'), realpath(__DIR__ . '/../')), 'classification-123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-03-c.md'), realpath(__DIR__ . '/../')), 'CLASSIFICATION  123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-04-d.md'), realpath(__DIR__ . '/../')), '  CLASSIFICATION 123');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-05-e.md'), realpath(__DIR__ . '/../')), 'ClassificatioN 123 ');
        $taxonomy->addFile($this->mockFile(realpath(__DIR__ . '/../Mocks/TaxonomyMocks/2016-01-05-f.md'), realpath(__DIR__ . '/../')), '   ClassificatioN 123 ');

        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList()['classification-123']), $descArrayA, $descArrayB));
        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList('DESC')['classification-123']), $descArrayA, $descArrayB));
        $this->assertTrue($this->isOr(array_keys($taxonomy->getFileList('ASC')['classification-123']), $ascArrayA, $ascArrayB));
    }

    private function isOr($test, $checkA, $checkB) {

        $constraintA = new IsEqual(
            $checkA,
            0.0,
            10,
            false,
            false
        );

        if ($constraintA->evaluate($test, '', true) === true){
            return true;
        }

        $constraintB = new IsEqual(
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
