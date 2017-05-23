<?php

namespace Tapestry\Tests\Traits;

use Symfony\Component\Finder\SplFileInfo;
use Tapestry\Entities\File;
use Tapestry\Modules\Content\FrontMatter;

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
    protected function getRelativePath($base, $path) {
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
     * @return File
     */
    protected function mockFile($filePath, $base = __DIR__ . '/..')
    {
        $base = realpath($base);
        $file = new File(new SplFileInfo($filePath, $this->getRelativePath($base, $filePath), $this->getRelativePath($base, $filePath)));
        $frontMatter = new FrontMatter($file->getFileContent());
        $file->setData($frontMatter->getData());
        $file->setContent($frontMatter->getContent());
        $file->getUid(); // Force the file to generate its uid
        return $file;
    }
}
