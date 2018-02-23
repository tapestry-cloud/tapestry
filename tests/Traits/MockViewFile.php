<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\Configuration;
use Tapestry\Entities\Project;
use Tapestry\Entities\ProjectFile;
use Tapestry\Entities\ViewFile;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\Renderers\ContentRendererFactory;
use Tapestry\Tapestry;

trait MockViewFile
{
    /**
     * @param Tapestry $tapestry
     * @param $viewPath
     * @param bool $render
     * @return ViewFile
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function mockViewFile(Tapestry $tapestry, string $viewPath, bool $render = false) : ViewFile
    {
        $file = new ProjectFile(new SplFileInfo($viewPath, '', ''));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->loadContent($frontMatter->getContent());

        /** @var Project $project */
        $project = $tapestry->getContainer()->get(Project::class);

        if ($render === true) {
            /** @var ContentRendererFactory $contentRenderers */
            $contentRendererFactory = new ContentRendererFactory();
            /** @var Configuration::class $configuration */
            $configuration = $tapestry->getContainer()->get(Configuration::class);
            foreach ($configuration->get('content_renderers') as $renderer) {
                $contentRendererFactory->add($tapestry->getContainer()->get($renderer));
            }
            $contentRendererFactory->renderFile($file);
        }

        $project->set('compiled', [
            $file->getUid() => $file,
        ]);

        return new ViewFile($project, $file->getUid());
    }
}