<?php

namespace Donato\MediaOrganizer;

class FileLister
{
    /** @var Directory */
    protected $dir;

    /** @var string */
    protected $allowedExtensions;

    /**
     * FileReader constructor.
     * @param string $dir
     * @param string $allowedExtensions
     */
    public function __construct($dir, $allowedExtensions)
    {
        $this->dir = new Directory($dir);
        $this->allowedExtensions = $allowedExtensions;
    }

    public function getFiles()
    {

        $allowedExt = $this->allowedExtensions;
        $pattern = $this->dir.DIRECTORY_SEPARATOR.'*';

        $files = array_filter(
            glob($pattern),
            function ($value) use ($allowedExt) {
                return preg_match($allowedExt, $value) > 0;
            }
        );

        return $files;
    }

    /**
     * @return Directory
     */
    public function getDirectory()
    {
        return $this->dir;
    }
}
