<?php

namespace Tapestry\Entities\Generators;

use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Entities\Permalink;
use Tapestry\Entities\Pagination;

class PaginationGenerator extends FileGenerator
{
    /**
     * @param Project $project
     *
     * @return \Tapestry\Entities\ProjectFileInterface|\Tapestry\Entities\ProjectFileInterface[]
     */
    public function generate(Project $project)
    {
        $newFile = clone $this->file;
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function ($value) {
                return $value !== 'PaginationGenerator';
            }),
        ]);

        if (! $configuration = $this->file->getData('pagination')) {
            return $newFile;
        }

        $defaultConfiguration = [
            'perPage' => 5,
            'skip' => 0
        ];

        $configuration = array_merge($defaultConfiguration, $configuration);
        $paginationKey = $configuration['provider'].'_items';

        if (! $paginationItems = $this->file->getData($paginationKey)) {
            return $newFile;
        }

        $paginationItems = array_filter($paginationItems, function ($key) use ($project) {
            return $project->has('files.'.$key);
        }, ARRAY_FILTER_USE_KEY);

        // Segment $paginationItems into n segments based upon perPage and create a clone of the file with that page's items
        // also update the files permalink and uid (unless its the first page). Follow up by injecting an instance of Paginator
        // which is loaded with previous/next links and how many total pages there are.

        $totalPages = ceil(count($paginationItems) / $configuration['perPage']);

        // If there are no pages because the pagination filter cleared out the array then return; otherwise this ends
        // up with an infinite loop
        if ($totalPages == 0) {
            return $newFile;
        }

        $generatedFiles = [];

        $currentPage = 0;
        foreach (array_chunk($paginationItems, $configuration['perPage'], true) as $pageItems) {
            $pageFile = clone $newFile;
            $currentPage++;
            $pageFile->setData(['pagination' => new Pagination($project, $pageItems, $totalPages, ($currentPage))]);

            if ($currentPage > 1) {
                $pageFile->setUid($pageFile->getUid().'_page_'.$currentPage);

                $permalink = $pageFile->getPermalink();
                $template = $permalink->getTemplate();

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

                if ($this->file->getFilename() === 'index') {
                    $parts = array_filter(explode('/', $template), function ($value) {
                        return $value !== '{filename}';
                    });

                    $template = implode('/', $parts);
                }

                $pageFile->setPermalink(new Permalink($template));
            }

            array_push($generatedFiles, $pageFile);
            unset($pageFile);
        }

        $totalGenerated = count($generatedFiles);
        if ($totalGenerated > 1) {
            /**
             * @var int
             * @var File $generatedFile
             */
            foreach ($generatedFiles as $key => &$generatedFile) {
                /*
                 * @var Pagination
                 * @var null|File  $previous
                 * @var null|File  $next
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
