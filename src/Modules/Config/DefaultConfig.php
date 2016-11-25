<?php

return [
    /**
     * Enable / Disable debugging
     */
    'debug' => false,

    /**
     * The site kernel to be loaded during site building
     */
    'kernel' => \Tapestry\Modules\Kernel\DefaultKernel::class,

    /**
     * Enable / Disable pretty permalink, if enabled then /about.md will be written as /about/index.md.
     * This may be over-ridden on a per file basis.
     */
    'pretty_permalink' => true,

    /**
     * Tapestry Content Types
     * @todo write Collections and Generators that use the below
     */
    'content_types' => [
        'blog' => [
            'path' => '_blog',
            'template' => 'blog',
            'permalink' => 'blog/{year}/{slug}.html',
            'enabled' => true,
            'taxonomies' => [
                'tags',
                'categories'
            ]
        ]
    ],

    'content_renderers' => [
        \Tapestry\Entities\Renderers\PlatesRenderer::class,
        \Tapestry\Entities\Renderers\HTMLRenderer::class,
        \Tapestry\Entities\Renderers\MarkdownRenderer::class,
        \Tapestry\Entities\Renderers\DefaultRenderer::class
    ],

    'content_generators' => [
        \Tapestry\Entities\Generators\PaginationGenerator::class,
        \Tapestry\Entities\Generators\TaxonomyArchiveGenerator::class,
        \Tapestry\Entities\Generators\TaxonomyIndexGenerator::class,
        Tapestry\Entities\Generators\CollectionItemGenerator::class,
    ],

    /**
     * Paths to ignore and not parse, any path matching those listed here will not be loaded.
     * Note: Must be valid regex.
     */
    'ignore' => [
        '_assets'
    ],

    /**
     * Paths that have been ignored, but which should be copied 1-to-1 from source to destination. This is useful for
     * ensuring that assets are copied, but are not parsed (which would slow things down with many files.)
     *
     * Note: Must be valid regex, and items within the copy array must exist within the ignore array otherwise
     * they will be ignored.
     */
    'copy' => []
];