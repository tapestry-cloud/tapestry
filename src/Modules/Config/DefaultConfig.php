<?php

return [
    /*
     * Enable / Disable debugging
     */
    'debug' => false,

    /*
     * The site kernel to be loaded during site building
     */
    'kernel' => Tapestry\Modules\Kernel\DefaultKernel::class,

    /*
     * Enable / Disable pretty permalink, if enabled then /about.md will be written as /about/index.md.
     * This may be over-ridden on a per file basis.
     */
    'pretty_permalink' => true,

    /*
     * Enable / Disable the publishing of files with `draft: true` in their front matter
     */
    'publish_drafts' => false,

    /*
     * Tapestry Content Types
     */
    'content_types' => [
        'blog' => [
            'path'       => '_blog',
            'template'   => '_views/blog',
            'permalink'  => 'blog/{year}/{slug}.{ext}',
            'enabled'    => true,
            'taxonomies' => [
                'tags',
                'categories',
            ],
        ],
    ],

    'content_collectors' => [
        'default' => [
            'collector' => Tapestry\Modules\Collectors\FilesystemCollector::class,
            'sourcePath' => '%sourceDirectory%',
            'mutatorCollection' => [
                Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator::class,
                Tapestry\Modules\Collectors\Mutators\FrontMatterMutator::class,
                Tapestry\Modules\Collectors\Mutators\IsScheduledMutator::class,
                Tapestry\Modules\Collectors\Mutators\IsIgnoredMutator::class,
            ],
            'filterCollection' => [
                Tapestry\Modules\Collectors\Exclusions\DraftsExclusion::class,
                Tapestry\Modules\Collectors\Exclusions\ConfigurationIgnoredExclusion::class,
            ],
        ],
    ],

    'content_renderers' => [
        Tapestry\Entities\Renderers\PlatesRenderer::class,
        Tapestry\Entities\Renderers\HTMLRenderer::class,
        Tapestry\Entities\Renderers\MarkdownRenderer::class,
        Tapestry\Entities\Renderers\DefaultRenderer::class,
    ],

    'content_generators' => [
        Tapestry\Entities\Generators\PaginationGenerator::class,
        Tapestry\Entities\Generators\TaxonomyArchiveGenerator::class,
        Tapestry\Entities\Generators\TaxonomyIndexGenerator::class,
        Tapestry\Entities\Generators\CollectionItemGenerator::class,
    ],

    /*
     * Compile steps that the build command will process.
     */
    'steps' => [
        Tapestry\Modules\Kernel\BootKernel::class,
        Tapestry\Modules\Content\ReadCache::class,
        Tapestry\Modules\Scripts\Before::class,
        Tapestry\Modules\Content\Clear::class,
        Tapestry\Modules\ContentTypes\LoadContentTypes::class,
        Tapestry\Modules\Renderers\LoadContentRenderers::class,
        Tapestry\Modules\Generators\LoadContentGenerators::class,
        Tapestry\Modules\Content\LoadSourceFiles::class,
        Tapestry\Modules\Api\Json::class,
        Tapestry\Modules\ContentTypes\ParseContentTypes::class,
        Tapestry\Modules\Content\Compile::class,
        Tapestry\Modules\Content\WriteFiles::class,
        Tapestry\Modules\Content\WriteCache::class,
        Tapestry\Modules\Content\Copy::class,
        Tapestry\Modules\Content\Clean::class,
        Tapestry\Modules\Scripts\After::class,
    ],

    /*
     * Paths to ignore and not parse, any path matching those listed here will not be loaded.
     */
    'ignore' => [
        '_assets',
    ],

    /*
     * Paths that have been ignored, but which should be copied 1-to-1 from source to destination. This is useful for
     * ensuring that assets are copied, but are not parsed (which would slow things down with many files.)
     *
     * Note: Items within the copy array must exist within the ignore array otherwise
     * they will be ignored.
     */
    'copy' => [],
];
