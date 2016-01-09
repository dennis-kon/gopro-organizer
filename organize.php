#!/usr/bin/php -q
<?php
$config          = parse_ini_file('config.ini');
$destinationRoot = $config['destination'];

if ($argc < 2) {
    die(printf('Usage %s <microSD path>/DCIM/100GOPRO'.PHP_EOL, $argv[0]));
}
$dir = realpath($argv[1]);

if (!file_exists($dir) || !is_dir($dir)) {
    die(printf('Directory not found.'.PHP_EOL));
}
$pattern = $dir.DIRECTORY_SEPARATOR.'*MP4';

$videos = glob($pattern);

if (count($videos) <= 0) {
    die(printf('No videos found.'.PHP_EOL));
}

$folders = [];
foreach ($videos as $video) {
    $timestamp = filemtime($video);
    $year      = date('Y', $timestamp);
    $month     = date('Y-m', $timestamp);
    $date      = date('Y-m-d', $timestamp);

    $destination = implode(DIRECTORY_SEPARATOR,
        [
        $destinationRoot,
        $year,
        $month,
        $date
    ]);

    $filename = str_replace($dir.DIRECTORY_SEPARATOR, '', $video);

    $folders[$destination][] = [
        'origin' => $video,
        'filename' => $filename
    ];

    //echo sprintf("%s - %s", $video, $date).PHP_EOL;
}

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
