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
    'pretty_permalinks' => true,

    /**
     * Tapestry Content Types
     * @todo write Collections and Generators that use the below
     */
    'content_types' => [
        'blog' => [
            'path' => '_posts',
            'template' => 'blog',
            'permalink' => 'blog/%year%/%slug%.html',
            'enabled' => true,
            'taxonomies' => [
                'tags',
                'categories'
            ]
        ]
    ],

    /**
     * Paths to ignore and not parse, any path matching those listed here will not be loaded.
     * Note: Must be valid regex.
     */
    'ignore' => [
        '_templates\/(.*?)'
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