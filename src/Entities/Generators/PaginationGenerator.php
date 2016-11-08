<?php namespace Tapestry\Entities\Generators;

use Tapestry\Entities\Pagination;
use Tapestry\Entities\Permalink;
use Tapestry\Entities\Project;

class PaginationGenerator extends FileGenerator
{

    /**
     * @param Project $project
     * @return \Tapestry\Entities\ProjectFileInterface|\Tapestry\Entities\ProjectFileInterface[]
     */
    public function generate(Project $project)
    {
        $newFile = clone($this->file);
        $newFile->setData([
            'generator' => array_filter($this->file->getData('generator'), function ($value) {
                return $value !== 'PaginationGenerator';
            })
        ]);

        if (!$configuration = $this->file->getData('pagination')) {
            return $newFile;
        }

        $defaultConfiguration = [
            'perPage' => 5
        ];

        $configuration = array_merge($defaultConfiguration, $configuration);
        $paginationKey = $configuration['provider'] . '_items';

        if (!$paginationItems = $this->file->getData($paginationKey)) {
            return $newFile;
        }

        // Segment $paginationItems into n segments based upon perPage and create a clone of the file with that page's items
        // also update the files permalink and uid (unless its the first page). Follow up by injecting an instance of Paginator
        // which is loaded with previous/next links and how many total pages there are.

        $totalPages = ceil(count($paginationItems) / $configuration['perPage']);
        $generatedFiles = [];

        $currentPage = 0;
        foreach (array_chunk($paginationItems, $configuration['perPage'], true) as $pageItems) {
            $pageFile = clone($newFile);
            $currentPage++;
            $pageFile->setData(['pagination' => new Pagination($pageItems, $totalPages, ($currentPage))]);

            if ($currentPage > 1) {
                $pageFile->setUid($pageFile->getUid() . '_page_' . $currentPage);

                $permalink = $pageFile->getPermalink();
                $template = $permalink->getTemplate();

                if (strpos($template, '.')) {
                    $parts = explode('.', $template);
                    $parts[0] .= '/{page}';
                    $template = implode('.', $parts);
                } else {
                    $template .= '/{page}';
                }

                $pageFile->setPermalink(new Permalink($template));
            }

            array_push($generatedFiles, $pageFile);
            unset($pageFile);
        }

        return $generatedFiles;
    }

}