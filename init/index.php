<?php

use Engine\App\App as Application;

if (file_exists(dirname(__FILE__) . '/config.php')) {
    require_once dirname(__FILE__)  . '/config.php';
}

if (is_dir(ENGINE_DIR)) {
    foreach(glob(ENGINE_DIR . '*') as $file) {
        if (file_exists($file)) require_once $file;
    }
}

## Require autoload
if(file_exists(LIBRARY_DIR . "/autoload.php")) {
    require_once LIBRARY_DIR . "/autoload.php";
}

## Launch aplication
$app = new Application();
$app->bootstrap(DIR_PATH . 'build_launch.php');
$app->bootstrap(DIR_PATH . 'database/init.php');

## Save app for implementation
$GLOBALS['app'] = $app;