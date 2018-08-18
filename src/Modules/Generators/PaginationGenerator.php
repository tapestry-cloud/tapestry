<?php

namespace Tapestry\Modules\Generators;

use Tapestry\Entities\Pagination;
use Tapestry\Entities\Project;
use Tapestry\Modules\Source\ClonedSource;
use Tapestry\Modules\Source\SourceInterface;

class PaginationGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Run the generation and return an array of generated
     * files (oddly implementing SourceInterface, naming
     * things is hard!)
     *
     * @param Project $project
     * @return array|SourceInterface[]
     * @throws \Exception
     */
    public function generate(Project $project): array
    {
        // @todo the previous version of this generator cloned $this->source. Is there a reason for that?

        // Remove reference to this Generator from source (otherwise it will execute again forever)
        $this->source->setData('generator', array_filter($this->source->getData('generator', []), function ($v) {
            return $v !== 'PaginationGenerator';
        }));

        if (! $configuration = $this->source->getData('pagination')) {
            return [$this->source];
        }

        // Merge configuration with defaults.
        $configuration = array_merge([
            'perPage' => 5,
            'skip' => 0,
        ], $configuration);

        if (! isset($configuration['provider'])) {
            return [$this->source];
        }

        $paginationKey = $configuration['provider'].'_items';

        if (! $paginationItems = $this->source->getData($paginationKey)) {
            return [$this->source];
        }

        $paginationItems = array_filter($paginationItems, function ($key) use ($project) {
            return $project->has('files.'.$key);
        }, ARRAY_FILTER_USE_KEY);

        // @todo is there a bug here that if the file exists in files.$key but is hidden then it gets included here anyway?
        // @todo can files be hidden?

        // Segment $paginationItems into n segments based upon perPage and create a clone of the file with that page's items
        // also update the files permalink and uid (unless its the first page). Follow up by injecting an instance of Paginator
        // which is loaded with previous/next links and how many total pages there are.

        $totalPages = ceil(count($paginationItems) / $configuration['perPage']);

        // If there are no pages because the pagination filter cleared out the array then return; otherwise this ends
        // up with an infinite loop
        if ($totalPages == 0) {
            return [$this->source];
        }

        $generatedFiles = [];

        // Skip functionality for #147
        // @link https://github.com/carbontwelve/tapestry/issues/147
        if ($configuration['skip'] > 0) {
            $paginationItems = array_slice($paginationItems, $configuration['skip'], null, true);
        }

        $currentPage = 0;

        // For each page of the pagination we need to "clone" $this->source in version one of Tapestry that
        // was done with clone. In version two we create a new ClonedSource object by passing in the parent
        // and then modifying that..

        foreach (array_chunk($paginationItems, $configuration['perPage'], true) as $pageItems) {
            $pageFile = new ClonedSource($this->source);

            $currentPage++;
            $pageFile->setData('pagination', new Pagination($project, $pageItems, $totalPages, ($currentPage)));

            if ($currentPage > 1) {
                $pageFile->setUid($pageFile->getUid().'_page_'.$currentPage);
                $template = $pageFile->getData('permalink', '');

                if (strpos($template, '.')) {
                    $parts = explode('.', $template);
                    $parts[0] .= '/{page}';
                    $template = implode('.', $parts);
                } else {
                    $template .= '/{page}';
                }

                // If the page calling this generator is, itself an index then we need to strip {filename} from the $template
                // because nobody expects page one to be /blog/index.html and page two to be blog/index/2/index.html
                // as per issue #50

                if ($this->source->getFilename() === 'index') {
                    $parts = array_filter(explode('/', $template), function ($value) {
                        return $value !== '{filename}';
                    });

                    $template = implode('/', $parts);
                }

                $pageFile->setData('permalink', $template);
            }

            array_push($generatedFiles, $pageFile);
        }unset($pageFile);

        $totalGenerated = count($generatedFiles);

        if ($totalGenerated > 1) {
            /**
             * @var int
             * @var SourceInterface $generatedFile
             */
            foreach ($generatedFiles as $key => &$generatedFile) {
                /**
                 * @var Pagination $pagination
                 * @var null|SourceInterface $previous
                 * @var null|SourceInterface $next
                 */
                $pagination = $generatedFile->getData('pagination');

                $next = (isset($generatedFiles[($key + 1)])) ? $generatedFiles[($key + 1)] : null;
                $previous = (isset($generatedFiles[($key - 1)])) ? $generatedFiles[($key - 1)] : null;

                $pagination->setPreviousNext(
                    is_null($previous) ? null : $previous->getUid(),
                    is_null($next) ? null : $next->getUid()
                );

                $pagination->setPages($generatedFiles);
            }
            unset($generatedFile);
        }

        return $generatedFiles;
    }
}