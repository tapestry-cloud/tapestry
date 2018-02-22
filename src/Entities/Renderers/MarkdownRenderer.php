<?php

namespace Tapestry\Entities\Renderers;

use Michelf\MarkdownExtra;
use Tapestry\Entities\ProjectFile;

/**
 * Class MarkdownRenderer
 *
 * Mutate MD input file into a PHTML output file for intermediate compiling.
 *
 * @package Tapestry\Entities\Renderers
 */
class MarkdownRenderer implements RendererInterface
{
    /**
     * @var array ProjectFile extensions that this renderer supports
     */
    private $extensions = ['md', 'markdown'];

    /**
     * @var MarkdownExtra
     */
    private $markdown;

    /**
     * MarkdownRenderer constructor.
     *
     * @param MarkdownExtra $markdown
     */
    public function __construct(MarkdownExtra $markdown)
    {
        $this->markdown = $markdown;
    }

    /**
     * Returns the renderer name.
     *
     * @return string
     */
    public function getName()
    {
        return 'MarkdownRenderer';
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
     * @throws \Exception
     */
    public function render(ProjectFile $file)
    {
        return $this->markdown->transform($file->getContent());
    }

    /**
     * Returns the extension that the rendered output conforms to.
     *
     * @return string
     */
    public function getDestinationExtension($ext)
    {
        return 'phtml';
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

    /**
     * @param ProjectFile $file
     *
     * @return void
     * @throws \Exception
     */
    public function mutateFile(ProjectFile &$file)
    {
        if ($layout = $file->getData('layout')) {
            $file->loadContent('<?php $v->layout("'. $layout .'", $projectFile->getData()) ?>' . $file->getContent());
        }
    }
}
