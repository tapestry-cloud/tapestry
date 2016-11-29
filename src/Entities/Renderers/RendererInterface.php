<?php

namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\File;

/**
 * Interface RendererInterface.
 *
 * A renderer is a class that deals with the actual rendering of a file within a collection.
 * The Manager class loops over all found files and identifies their renderer via the
 * canRender method. A generator then uses the render method to generate the page.
 */
interface RendererInterface
{
    /**
     * Returns an array of the extensions that this renderer will support.
     *
     * @return array
     */
    public function supportedExtensions();

    /**
     * Returns true if the renderer can render the given extension.
     *
     * @param string $extension
     *
     * @return bool
     */
    public function canRender($extension);

    /**
     * Render the input file content and return the output.
     *
     * @param File $file
     *
     * @return string
     */
    public function render(File $file);

    /**
     * Returns the extension that the rendered output conforms to.
     *
     * @return string
     */
    public function getDestinationExtension($ext);

    /**
     * Does this renderer support frontmatter?
     *
     * @return bool
     */
    public function supportsFrontMatter();

    /**
     * A Renderer can mutate an input file so that the Compile class pushes it through to another renderer, this is used
     * by the HTMLRenderer to pass through a html file that has a template to the PlatesRenderer.
     *
     * @param File $file
     *
     * @return void
     */
    public function mutateFile(File &$file);
}
