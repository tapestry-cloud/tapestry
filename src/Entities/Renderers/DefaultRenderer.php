<?php

namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\File;

class DefaultRenderer implements RendererInterface
{
    /**
     * @var array File extensions that this renderer supports
     */
    private $extensions = ['*'];

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
     * Returns true if the renderer can render the given extension.
     *
     * @param string $extension
     *
     * @return bool
     */
    public function canRender($extension)
    {
        return in_array($extension, $this->extensions);
    }

    /**
     * Render the input file content and return the output.
     *
     * @param File $file
     *
     * @return string
     */
    public function render(File $file)
    {
        return '';
    }

    /**
     * Returns the extension that the rendered output conforms to.
     *
     * @return string
     */
    public function getDestinationExtension($ext)
    {
        return $ext;
    }

    /**
     * Does this renderer support frontmatter?
     *
     * @return bool
     */
    public function supportsFrontMatter()
    {
        return false;
    }

    /**
     * The default action is to set a File for copying and therefore
     * disable its pretty permalink output.
     *
     * @param File $file
     * @return void
     */
    public function mutateFile(File &$file)
    {
        $file->setToCopy(true);
        $file->setData([
            'pretty_permalink' => false,
        ]);
    }
}
