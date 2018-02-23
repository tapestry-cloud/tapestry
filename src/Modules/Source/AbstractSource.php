<?php

namespace Tapestry\Modules\Source;

use Tapestry\Entities\Permalink;

abstract class AbstractSource implements SourceInterface
{

    /**
     * File meta data, usually from front matter or site config.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * The Permalink object attached to this file.
     *
     * @var Permalink
     */
    protected $permalink;

    /**
     * This is the files rendered content as set by `setRenderedContent`.
     *
     * @var bool|string
     */
    protected $content = false;

}