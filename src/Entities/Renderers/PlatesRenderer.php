<?php

namespace Tapestry\Entities\Renderers;

use League\Plates\Engine;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFile;

/**
 * Class PlatesRenderer.
 *
 * Pass through phtml files for intermediate compiling.
 */
class PlatesRenderer implements RendererInterface
{
    /**
     * @var array File extensions that this renderer supports
     */
    private $extensions = ['phtml', 'php'];

    /**
     * @var Engine
     */
    private $parser;

    /**
     * @var Project
     */
    private $project;

    /**
     * PlatesRenderer constructor.
     *
     * @param Engine  $parser
     * @param Project $project
     */
    public function __construct(Engine $parser, Project $project)
    {
        $this->parser = $parser;
        $this->project = $project;
        //$this->parser->setProject($project); //@todo determine if this is nessessary with v4
    }

    /**
     * Returns the renderer name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PlatesRenderer';
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
        if (! str_contains($file->getContent(), '$v->layout')) {
            if ($layout = $file->getData('layout')) {
                $file->loadContent('<?php $v->layout("'.$layout.'", $projectFile->getData()) ?>'.$file->getContent());
            }
        }
    }
}
