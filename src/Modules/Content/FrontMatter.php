<?php

namespace Tapestry\Modules\Content;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class FrontMatter
{
    /**
     * @var string
     */
    private $pattern = '/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s';

    /**
     * @var string
     */
    private $body = '';
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var string
     */
    private $content = '';

    /**
     * Frontmatter constructor.
     *
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
        // If front matter is found, then we should parse it
        if (preg_match($this->pattern, $this->body, $matches)) {
            $this->content = $matches[2];
            $this->parse($matches[1]);
        } else {
            $this->content = $this->body;
        }
    }

    /**
     * Return an array.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    private function parse($string)
    {
        if (! preg_match('/^(\s*[-]+\s*|\s*)$/', $string)) {
            try {
                $this->data = Yaml::parse($string);
            } catch (ParseException $e) {
                // Most likely not valid YAML
                $this->content = $this->body;
            }
        }
    }
}
