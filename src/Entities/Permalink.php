<?php namespace Tapestry\Entities;

class Permalink
{
    /**
     * @var string
     */
    private $template;

    /**
     * Permalink constructor.
     * @param string $template
     */
    public function __construct($template = '{path}{filename}.{ext}')
    {
        $this->setTemplate($template);
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getCompiled(File $file)
    {
        $n = 1;
        return $this->template;
    }
}