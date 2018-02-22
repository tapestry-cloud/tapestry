<?php

namespace Tapestry\Entities\Renderers;

use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\Project;
use Symfony\Component\Finder\SplFileInfo;

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
     * @param ProjectFile $file
     *
     * @return void
     * @throws \Exception
     */
    public function mutateFile(ProjectFile &$file)
    {
        //
        // If the HTML file has a template then we should pass it on to the plates renderer
        //
        if ($template = $file->getData('template')) {
            $templateRelativePath = '_templates'.DIRECTORY_SEPARATOR.$template.'.phtml';
            $templatePath = $this->project->sourceDirectory.DIRECTORY_SEPARATOR.$templateRelativePath;
            if (file_exists($templatePath)) {
                $fileName = $file->getFilename();
                $filePath = $file->getPath();

                $file->setRendered(false);
                $file->setData('content', $file->getContent());

                $file = new ProjectFile(new SplFileInfo($templatePath, '_templates', $templateRelativePath), $file->getData(), false);
                $file->loadContent($file->getFileContent());
                $file->setFilename($fileName);
                $file->setPath($filePath);
            }
        }
    }
}
