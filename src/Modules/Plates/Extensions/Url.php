<?php

namespace Tapestry\Modules\Plates\Extensions;

use League\Plates\Engine;
use League\Plates\Extension;

/**
 * Class Url.
 *
 * The Url extension to Plates provides the user the `url` method. This allows
 * you to parse the input `$uri` and have a valid url be returned that contains
 * the domain name for the current environment.
 *
 * e.g
 *
 * `url('/abc/123/')` -> 'http://www.example.com/abc/123'
 */
class Url implements Extension
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

    /**
     * Register the `url` helper with Plates.
     *
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->addMethods(['url' => [$this, 'url']]);
    }

    /**
     * Returns the input `$uri` parsed through the `Url` class.
     *
     * @see \Tapestry\Entities\Url
     * @param string $uri
     * @return string
     */
    public function url(string $uri = '') : string
    {
        return $this->url->parse($uri);
    }
}
