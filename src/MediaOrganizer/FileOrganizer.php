<?php

namespace Donato\MediaOrganizer;


class FileOrganizer
{
    /** @var array */
    protected $files;

    /**
     * FileOrganizer constructor.
     * @param array $files
     */
    public function __construct(array $files = [])
    {
        $this->files = $files;
    }

    /**
     * @param Directory $originRoot
     * @param Directory $destinationRoot
     * @return array
     */
    public function organize($originRoot, $destinationRoot)
    {
        $folders = [];
        foreach ($this->files as $file) {
            $timestamp = filemtime($file);
            $year = date('Y', $timestamp);
            $month = date('Y-m', $timestamp);
            $date = date('Y-m-d', $timestamp);

            $destination = implode(
                DIRECTORY_SEPARATOR,
                [
                    $destinationRoot,
                    $year,
                    $month,
                    $date,
                ]
            );

            $filename = str_replace($originRoot.DIRECTORY_SEPARATOR, '', $file);

            $folders[$destination][] = [
                'origin' => $file,
                'filename' => $filename,
            ];
        }

        return $folders;
    }
}