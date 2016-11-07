<?php namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\File;
use Tapestry\Entities\Project;
use Tapestry\Plates\Engine;

class PlatesRenderer implements RendererInterface
{
    /**
     * @var array File extensions that this renderer supports
     */
    private $extensions = ['phtml'];

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
     * @param Engine $parser
     * @param Project $project
     */
    public function __construct(Engine $parser, Project $project)
    {

        $this->parser = $parser;
        $this->project = $project;
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
        return $this->parser->renderFile($file, $this->project->currentWorkingDirectory . DIRECTORY_SEPARATOR . '.tmp');
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

    /**
     * @param File $file
     * @return void
     */
    public function mutateFile(File &$file)
    {
        // ...
    }
}