#!/usr/bin/php -q
<?php

require_once 'vendor/autoload.php';

use Donato\MediaOrganizer\Directory;
use Donato\MediaOrganizer\FileLister;
use Donato\MediaOrganizer\FileOrganizer;
use Donato\MediaOrganizer\Exception\DirectoryNotFoundException;

$config = parse_ini_file('config.ini', true);
$allowedExt = $config['media']['include_ext'];

if ($argc < 3) {
    die(printf('Usage %s <media path> <destination>'.PHP_EOL, $argv[0]));
}

try {
    $destinationRoot = new Directory($argv[2]);
    
    $fileLister = new FileLister($argv[1], $allowedExt);
    $files = $fileLister->getFiles();

    if (count($files) <= 0) {
        die(printf('No media found.'.PHP_EOL));
    }

    $organizer = new FileOrganizer($files);
    $folders = $organizer->organize($fileLister->getDirectory(), $destinationRoot);
} catch (DirectoryNotFoundException $e) {
    die(printf($e->getMessage().PHP_EOL));
}

$script = '#!/bin/bash'.PHP_EOL.PHP_EOL;
foreach ($folders as $folder => $files) {
    $script .= "echo Copying media to {$folder}...;".PHP_EOL;
    $script .= "mkdir -p {$folder};".PHP_EOL;
    $folderTotal = count($files);

    $i = 0;
    foreach ($files as $file) {
        $i++;
        $script .= "echo -n File {$i} of {$folderTotal}... ;".PHP_EOL;
        $script .= "cp \"{$file['origin']}\" \"{$folder}/{$file['filename']}\";".PHP_EOL;
        $script .= "echo [DONE];".PHP_EOL;
    }
}

file_put_contents('copy_media.sh', $script);
system('chmod +x copy_media.sh');
