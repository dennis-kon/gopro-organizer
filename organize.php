#!/usr/bin/php -q
<?php
require_once 'read_origin.php';

$script = '#!/bin/bash'.PHP_EOL.PHP_EOL;
foreach ($folders as $folder => $videos) {
    $script .= "echo Copying videos to {$folder}...;".PHP_EOL;
    $script .= "mkdir -p {$folder};".PHP_EOL;
    $folderTotal = count($videos);

    $i = 0;
    foreach ($videos as $video) {
        $i++;
        $script .= "echo -n Video {$i} of {$folderTotal}... ;".PHP_EOL;
        $script .= "cp \"{$video['origin']}\" \"{$folder}/{$video['filename']}\";".PHP_EOL;
        $script .= "echo [DONE];".PHP_EOL;
    }
}

file_put_contents('copy_videos.sh', $script);
system('chmod +x copy_videos.sh');
