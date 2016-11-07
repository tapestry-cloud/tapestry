<?php namespace Tapestry\Entities\Renderers;

use League\Plates\Engine;
use Tapestry\Entities\File;
use Tapestry\Entities\Project;

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
        $this->parser->setDirectory($this->project->sourceDirectory);
        $this->parser->setFileExtension('phtml');
        return $this->parser->render(
            $file->getFileInfo()->getRelativePath() .
            DIRECTORY_SEPARATOR .
            pathinfo($file->getFileInfo()->getFilename(), PATHINFO_FILENAME),
            $file->getData()
        );
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