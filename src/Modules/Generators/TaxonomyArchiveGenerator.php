<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Project;
use Tapestry\Modules\Source\SourceInterface;

/**
 * Class TaxonomyArchiveGenerator
 *
 * The purpose of this generator is to generate one source for each classification found in the taxonomy
 * configured in the frontmatter of the calling source. e.g
 *
 * use:
 *     - blog_tags
 * generator:
 *     - TaxonomyArchiveGenerator
 *
 * The above informs the generator that it must look up all classifications for the tags taxonomy found
 * within the blog content type and generate one new source file for each classification injecting
 * references to the source's that have been bucketed into each classification.
 *
 * ---
 *
 * This can be chained with the PaginationGenerator:
 *
 * use:
 *     - blog_tags
 * generator:
 *     - TaxonomyArchiveGenerator
 *     - PaginationGenerator
 * pagination:
 *     provider: blog_tags
 *     perPage: 6
 *
 * The above tells the pagination generator to use the blog_tags key as its source and paginate those
 * six per page.
 *
 * @todo is there a mechanism in place that defers the PaginationGenerator if its provider isn't ready?
 * @todo The above to to may reference an unknown bug, create an issue and write a unit test.
 */
class TaxonomyArchiveGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * @param Project $project
     * @return array|SourceInterface[]
     */
    public function generate(Project $project): array
    {



        // TODO: Implement generate() method.
    }
}