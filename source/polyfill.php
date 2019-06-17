<?php

/**
 * Polyfill
 * 
 */
function path_join(...$paths) {
    $purged_paths = [];

    foreach($paths as $key=>$path) {
        if (substr($path, 0, 1) == '/' && $key != 0) {
            $path = substr($path, 1);
        }
        
        if(substr($path, strlen($path) - 1, 1) == "/" && $key != count($paths) - 1) {
            $path = substr($path, 0, strlen($path) - 1);
        }
        
        array_push($purged_paths, $path);
    }

    return implode("/", $purged_paths);
}