<?php

namespace Tapestry\Entities;

use Tapestry\ArrayContainer;
use Tapestry\Entities\Generators\FileGenerator;
use Tapestry\Entities\Collections\FlatCollection;
use Tapestry\Entities\Tree\Tree;
use Tapestry\Modules\Source\SourceInterface;

class Project extends ArrayContainer
{
    /**
     * @var string
     */
    public $sourceDirectory;

    /**
     * @var string
     */
    public $destinationDirectory;

    /**
     * @var string
     */
    public $currentWorkingDirectory;

    /**
     * @var string
     */
    public $environment;

    /**
     * Project constructor.
     *
     * @param string $cwd
     * @param string $dist
     * @param string $environment
     */
    public function __construct(string $cwd, string $dist, string $environment)
    {
        $this->sourceDirectory = $cwd.DIRECTORY_SEPARATOR.'source';
        $this->destinationDirectory = $dist;

        $this->currentWorkingDirectory = $cwd;
        $this->environment = $environment;

        parent::__construct(
            [
                'files' => new FlatCollection(),
                'ast' => new Tree(),
            ]
        );
    }

    /**
     * @param SourceInterface|FileGenerator $file
     */
    public function addFile(SourceInterface $file)
    {
        $this->set('files.'.$file->getUid(), $file);
    }

    /**
     * @param string $key
     *
     * @return SourceInterface
     */
    public function getFile($key)
    {
        return $this->get('files.'.$key);
    }

    /**
     * @return SourceInterface[]|FlatCollection
     */
    public function allSources(): FlatCollection
    {
        return $this->get('files');
    }

    /**
     * @param SourceInterface|FileGenerator $file
     */
    public function removeFile(SourceInterface $file)
    {
        $this->remove('files.'.$file->getUid());
    }

    /**
     * @param SourceInterface|FileGenerator $oldFile
     * @param SourceInterface|FileGenerator $newFile
     */
    public function replaceFile(SourceInterface $oldFile, SourceInterface $newFile)
    {
        $this->removeFile($oldFile);
        $this->addFile($newFile);
    }

    /**
     * @param string $name
     * @param SourceInterface   $file
     *
     * @return ProjectFileGeneratorInterface
     */
    public function getContentGenerator($name, SourceInterface $file)
    {
        return $this->get('content_generators')->get($name, $file);
    }

    /**
     * @param string $name
     *
     * @return ContentType
     */
    public function getContentType($name)
    {
        return $this->get('content_types.'.$name);
    }

    /**
     * @return Tree
     * @throws \Exception
     */
    public function getAST(): Tree
    {
        if (! $this->has('ast')) {
            throw new \Exception('AST is not initiated');
        }
        return $this->get('ast');
    }
}
