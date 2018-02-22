<?php

namespace Tapestry\Entities;

class CachedFile
{
    /**
     * ProjectFile unique identifier.
     *
     * @var string
     */
    private $uid;

    /**
     * ProjectFile invalidation hash.
     *
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var array
     */
    private $layouts;

    /**
     * CachedFile constructor.
     *
     * @param ProjectFile $file
     * @param array $layouts
     * @param string $sourceDirectory
     * @throws \Exception
     */
    public function __construct(ProjectFile $file, array $layouts = [], $sourceDirectory = '')
    {
        $this->layouts = $layouts;
        $this->sourceDirectory = $sourceDirectory;
        $this->uid = $file->getUid();
        $this->hash = $this->hashFile($file);
    }

    /**
     * Check to see if the current cache entry is still valid.
     *
     * @param ProjectFile $file
     * @return bool
     * @throws \Exception
     */
    public function check(ProjectFile $file)
    {
        if ($file->getUid() !== $this->uid) {
            throw new \Exception('This CachedFile is not for uid ['.$file->getUid().']');
        }

        return $this->hash === $this->hashFile($file);
    }

    /**
     * Calculates the invalidation hash for the given ProjectFile.
     *
     * @param ProjectFile $file
     * @return string
     * @throws \Exception
     */
    private function hashFile(ProjectFile $file)
    {
        $arr = [];

        foreach ($this->layouts as $layout) {
            if (strpos($layout, '_templates') === false) {
                $layout = '_templates'.DIRECTORY_SEPARATOR.$layout;
            }

            $layoutPathName = $this->sourceDirectory.DIRECTORY_SEPARATOR.$layout.'.phtml';
            if (file_exists($layoutPathName)) {
                array_push($arr, sha1_file($layoutPathName));
            }
        }

        array_push($arr, $file->getMTime());

        return sha1(implode('.', $arr));
    }
}
