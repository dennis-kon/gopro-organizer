<?php

require_once 'vendor/autoload.php';

use Donato\MediaOrganizer\FileLister;
use Donato\MediaOrganizer\Exception\DirectoryNotFoundException;

$config = parse_ini_file('config.ini', true);
$destinationRoot = $config['gopro']['destination'];
$allowedExt = $config['gopro']['include_ext'];

if ($argc < 2) {
    die(printf('Usage %s <microSD path>/DCIM/100GOPRO'.PHP_EOL, $argv[0]));
}

try {
    $fileLister = new FileLister($argv[1], $allowedExt);
    $videos = $fileLister->getFiles();
} catch (DirectoryNotFoundException $e) {
    die(printf($e->getMessage().PHP_EOL));
}

if (count($videos) <= 0) {
    die(printf('No videos found.'.PHP_EOL));
}

$folders = [];
foreach ($videos as $video) {
    $timestamp = filemtime($video);
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

    $filename = str_replace($fileLister->getDirectory().DIRECTORY_SEPARATOR, '', $video);

    $folders[$destination][] = [
        'origin' => $video,
        'filename' => $filename,
    ];
}
