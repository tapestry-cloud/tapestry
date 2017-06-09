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
        return $this->siteUrl.'/'.$this->encode($uri);
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

    /**
     * @param string $uri
     * @return array
     */
    private function encode($uri = '') {
        $parts = explode('/', $uri);
        foreach ($parts as &$part) {
            if (strpos($part, 'index') !== false) {
                $part = null;
                continue;
            }
            $part = $this->encodePart($part);
        }
        unset($part);

        $parts = array_filter($parts, function ($value) {
            return ! is_null($value);
        });

        // If the last uri part doesn't contain a file name e.g. index.html or readme.txt

        $e = end($parts);
        if (
            strlen($e) > 0 &&
            strpos($e, '?') === false &&
            strpos($e, '=') === false &&
            strpos($e, '&') === false &&
            strpos($e, '#') === false &&
            strpos($e, '.') === false
        ) {
            array_push($parts, '');
        }

        return trim(implode('/', $parts));
    }

    /**
     * Ensures that uri parts containing ? or # get encoded correctly
     *
     * @param string $part
     * @return string
     */
    private function encodePart($part) {
        if (strpos($part, '?') !== false) {
            if (strpos($part, '?') === 0) {
                $output = '?';
                $part = substr($part, 1);
            }else {
                $pe = explode('?', $part);
                $output = $pe[0] . '/?';
                $part = $pe[1];
                unset($pe);
            }

            if (strpos($part, '&')){
                $parts = explode('&', $part);
            }else{
                $parts = [$part];
            }
            foreach ($parts as &$p) {
                if (strpos($p, '=') !== false) {
                    $pe = explode('=', $p);
                    $p = $pe[0] . '=' . urlencode($pe[1]);
                }
            }unset($p, $pe);

            $output .= implode('&', $parts);
            return $output;
        }

        if (strpos($part, '#') !== false) {
            if (strpos($part, '#') === 0) {
                $part = substr($part, 1);
                return '#' . rawurlencode($part);
            }
            $pe = explode('#', $part);
            return $pe[0] . '/#' . rawurlencode($pe[1]);
        }

        return rawurlencode($part);
    }
}
