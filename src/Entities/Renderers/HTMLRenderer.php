<?php namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\File;

class HTMLRenderer implements RendererInterface
{
    /**
     * @var array File extensions that this renderer supports
     */
    private $extensions = ['htm', 'html'];

    /**
     * Returns an array of the extensions that this renderer will support.
     *
     * @return array
     */
    public function supportedExtensions()
    {
        return $this->extensions;
    }

    /**
     * Returns true if the renderer can render the given extension
     *
     * @param string $extension
     * @return bool
     */
    public function canRender($extension)
    {
        return in_array($extension, $this->extensions);
    }

    /**
     * Render the input file content and return the output
     *
     * @param File $file
     * @return string
     */
    public function render(File $file)
    {
        return $file->getContent();
    }

    /**
     * Returns the extension that the rendered output conforms to
     *
     * @return string
     */
    public function getDestinationExtension()
    {
        return 'html';
    }

    /**
     * Does this renderer support frontmatter?
     *
     * @return bool
     */
    public function supportsFrontMatter()
    {
        return true;
    }
}