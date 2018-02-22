<?php

namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\ProjectFile;

class DefaultRenderer implements RendererInterface
{
    /**
     * @var array ProjectFile extensions that this renderer supports
     */
    private $extensions = ['*'];

    /**
     * Returns the renderer name.
     *
     * @return string
     */
    public function getName()
    {
        return 'DefaultRenderer';
    }

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
     * @param ProjectFile $file
     *
     * @return string
     */
    public function render(ProjectFile $file)
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
     * The default action is to set a ProjectFile for copying and therefore
     * disable its pretty permalink output.
     *
     * @param ProjectFile $file
     * @return void
     */
    public function mutateFile(ProjectFile &$file)
    {
        $file->setToCopy(true);
        $file->setData([
            'pretty_permalink' => false,
        ]);
    }
}
