<?php

namespace Tapestry\Modules\Content;

use Tapestry\Step;
use Tapestry\Entities\Project;
use Symfony\Component\Finder\Finder;
use Tapestry\Entities\Configuration;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

class Copy implements Step
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Copy constructor.
     * @param Configuration $configuration
     * @param Filesystem $filesystem
     */
    public function __construct(Configuration $configuration, Filesystem $filesystem)
    {
        $this->configuration = $configuration;
        $this->filesystem = $filesystem;
    }

    /**
     * Process the Project at current.
     *
     * @param Project $project
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function __invoke(Project $project, OutputInterface $output)
    {
        foreach ($this->configuration->get('copy') as $path) {
            $finder = new Finder();
            $basePath = $project->sourceDirectory.DIRECTORY_SEPARATOR.$path;

            if (! $this->filesystem->exists($basePath)) {
                $output->writeln('<error>[!]</error> Copy Path ['.$basePath.'] does not exist!');
                continue;
            }

            /** @var SplFileInfo $file */
            foreach ($finder->files()->in($basePath) as $file) {
                $inputPath = $project->sourceDirectory.
                    DIRECTORY_SEPARATOR.
                    $path.
                    DIRECTORY_SEPARATOR.
                    $file->getRelativePathname();

                $outputPath = $project->destinationDirectory.
                    DIRECTORY_SEPARATOR.
                    $path.
                    DIRECTORY_SEPARATOR.
                    $file->getRelativePathname();

                $output->writeln('[+] Copying Path ['.$inputPath.'] to path ['.$outputPath.']');
                $this->filesystem->copy($inputPath, $outputPath);
            }
        }

        return true;
    }
}
