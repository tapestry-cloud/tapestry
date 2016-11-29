<?php

return [
    /*
     * Some example global data, this is available from $this->site(...) in any phtml file
     */
    'site' => [
        'title'       => 'Tapestry Scaffold',
        'url'         => 'http://localhost:3000',
        'description' => 'Basic site scaffold for the Tapestry static site generator.',
        'author'      => 'Some One',
        'email'       => 'some.one@example.com',
    ],

    /*
     * The site kernel to be loaded during site building
     */
    'kernel' => \Site\SiteKernel::class,
];
