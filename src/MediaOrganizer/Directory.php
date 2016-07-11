<?php

namespace Donato\MediaOrganizer;


use Donato\MediaOrganizer\Exception\DirectoryNotFoundException;

class Directory
{
    /** @var string */
    protected $dir;

    /**
     * Directory constructor.
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->checkDir($dir);
        $this->dir = realpath($dir);
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->getDirectory();
    }

    /**
     * @param string $dir
     * @throws DirectoryNotFoundException
     */
    private function checkDir($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            throw new DirectoryNotFoundException("Directory not found: $dir");
        }
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getDirectory();
    }
}
