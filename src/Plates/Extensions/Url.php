<?php

namespace Tapestry\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class Url implements ExtensionInterface
{
    /**
     * @var \Tapestry\Entities\Url
     */
    private $url;

    /**
     * Url constructor.
     *
     * @param \Tapestry\Entities\Url $url
     */
    public function __construct(\Tapestry\Entities\Url $url)
    {
        $this->url = $url;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('url', [$this, 'url']);
    }

    public function url($uri = '')
    {
        return $this->url->parse($uri);
    }
}
