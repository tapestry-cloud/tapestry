<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Modules\Collectors\Mutators\SetDateDataFromFileNameMutator;
use Tapestry\Modules\Content\FrontMatter;
use Tapestry\Modules\Source\SplFileSource;

trait MockFile {
    /**
     * Return a relative path to a file or directory using base directory.
     * When you set $base to /website and $path to /website/store/library.php
     * this function will return /store/library.php
     *
     * @param   String   $base   A base path used to construct relative path. For example /website
     * @param   String   $path   A full path to file or directory used to construct relative path. For example /website/store/library.php
     *
     * @return  String
     */
    protected function getRelativePath(string $base, string $path) {
        // On windows strip drive letter
        $base = preg_replace('/^[A-Z]:/i', '', $base);
        $path = preg_replace('/^[A-Z]:/i', '', $path);

        // Normalise separator
        $base = str_replace(['/', '\\'], '/', $base);
        $path = str_replace(['/', '\\'], '/', $path);
        $separator = '/';

        $base = array_slice(explode($separator, rtrim($base,$separator)),1);
        $path = array_slice(explode($separator, rtrim($path,$separator)),1);

        return $separator.implode($separator, array_slice($path, count($base)));
    }

    /**
     * Mock a Tapestry File object.
     *
     * @param $filePath
     * @param string $base
     *
     * @return SplFileSource
     * @throws \Exception
     */
    protected function mockFile(string $filePath, string $base = __DIR__ . '/..') : SplFileSource
    {
        $base = realpath($base);
        $file = new SplFileSource(new SplFileInfo($filePath, $this->getRelativePath($base, $filePath), $this->getRelativePath($base, $filePath)));

        $frontMatter = new FrontMatter($file->getRawContent());
        $file->setData($frontMatter->getData());
        $file->setRenderedContent($frontMatter->getContent());

        (new SetDateDataFromFileNameMutator())->mutate($file);

        return $file;
    }
}
