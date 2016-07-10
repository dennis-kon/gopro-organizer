#!/usr/bin/php -q
<?php
require_once 'read_origin.php';

$copied = [];
$pending = [];
foreach ($folders as $folder => $videos) {
    foreach ($videos as $video) {
        $dest = $folder.DIRECTORY_SEPARATOR.$video['filename'];
        $origin = $video['origin'];
        $exists = file_exists($dest);
        if ($exists && filesize($dest) === filesize($origin)) {
            $status = 'OK';
        } else {
            $status = 'PENDING';
        }
        echo $video['filename'].' '.$status.PHP_EOL;
    }
}
