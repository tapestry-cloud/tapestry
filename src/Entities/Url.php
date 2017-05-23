<?php

namespace Tapestry\Entities;

class Url
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * Site constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function parse($uri = '')
    {
        $this->loadSiteUrl();
        $uri = $this->cleanUri($uri);

        $parts = explode('/', $uri);
        foreach ($parts as &$part) {
            if (strpos($part, 'index') !== false) {
                $part = null;
                continue;
            }
            $part = rawurlencode($part);
        }
        unset($part);

        $parts = array_filter($parts, function ($value) {
            return ! is_null($value);
        });

        return $this->siteUrl.'/'.implode('/', $parts);
    }

    private function loadSiteUrl()
    {
        if (! is_null($this->siteUrl)) {
            return $this->siteUrl;
        }
        if (! $this->siteUrl = $this->configuration->get('site.url')) {
            throw new \Exception('The site url is not set in your site configuration.');
        }

        $this->siteUrl = $this->cleanUri($this->siteUrl);

        return $this->siteUrl;
    }

    private function cleanUri($text)
    {
        $text = trim($text);

        if (substr($text, 0, 1) === '/') {
            $text = substr($text, 1);
        }

        return ($text === false) ? '' : $text;
    }
}
