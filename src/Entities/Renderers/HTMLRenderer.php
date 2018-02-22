<?php

namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\Project;

/**
 * Class HTMLRenderer
 *
 * Mutate HTML input file into a PHTML output file for intermediate compiling.
 *
 * @package Tapestry\Entities\Renderers
 */
class HTMLRenderer implements RendererInterface
{
    /**
     * @var array File extensions that this renderer supports
     */
    private $extensions = ['htm', 'html'];

    /**
     * @var Project
     */
    private $project;

    /**
     * HTMLRenderer constructor.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Returns the renderer name.
     *
     * @return string
     */
    public function getName()
    {
        return 'HTMLRenderer';
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
        return $file->getContent();
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
