<?php

function getDirs($dir, &$results = array())
{
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (is_dir($path) && $value != "." && $value != "..") {
            $results[] = $path;
        }
    }
    return $results;
}

function getDirContents($dir, &$results = array(), $ALLOW_PREFIX = ["php"], $IGNORE_DIR = [])
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $array = explode(".", $path);
            $FLAG = false;
            foreach ($IGNORE_DIR as $i_dir) {
                if (strpos($path, $i_dir)) $FLAG = true;
            }
            if ($FLAG) continue;
            if (in_array(end($array), $ALLOW_PREFIX)) $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results, $ALLOW_PREFIX,$IGNORE_DIR);
        }
    }
    return $results;
}


