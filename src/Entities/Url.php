<?php namespace Tapestry\Entities;

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

        if (strpos($uri, 'index') === false) {
            return $this->siteUrl . '/' . $uri;
        }

        $parts = explode('/', $uri);
        array_pop($parts);

        return $this->siteUrl . '/' . implode('/', $parts);
    }

    private function loadSiteUrl()
    {
        if (!is_null($this->siteUrl)) {
            return $this->siteUrl;
        }
        if (!$this->siteUrl = $this->configuration->get('site.url')) {
            throw new \Exception('The site url is not set in your site configuration.');
        }

        $this->siteUrl = $this->cleanUri($this->siteUrl);
        return $this->siteUrl;
    }

    private function cleanUri($text)
    {
        if (substr($text, 0, 1) === '/') {
            $text = substr($text, 1);
            if ($text === false){ $text = ''; }
        }

        return $text;
    }
}