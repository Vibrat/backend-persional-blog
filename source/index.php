<?php
 /**
  * APPLICATION BOOTSTRAP
  * 1. Require config
  * 2. Require engine
  * 3. Require injection: Router, Model, View, Controller
  * 4. Require Router performance and load input.
  */

  use Engine\App\App as Application;

  ## Add Polyfill
  require_once "./polyfill.php";

  ## load config
  if (file_exists('./config.php')) {
      require_once './config.php';
  }

  ## create engine
  if(is_dir(ENGINE_DIR)) {
    foreach(glob(ENGINE_DIR . "*") as $file) {
        if (file_exists($file)) require_once $file;
    }
  }

  ## Load Model
  if (is_dir(MODEL_DIR)) {
      foreach(glob(MODEL_DIR . "*") as $file) {
        if (file_exists($file)) require_once $file;
      }
  }

   ## Require autoload
   if(file_exists(LIBRARY_DIR . "/autoload.php")) {
    require_once LIBRARY_DIR . "/autoload.php";
  }

  ## Load vendor
  if(is_dir(VENDOR_DIR) && file_exists( $vendor = VENDOR_DIR . "autoload.php")) {
        require_once $vendor;
  }


  ## Launch aplication
  $app = new Application();
  $app->bootstrap('./launch.php');

  ## Save app for implementation
  $GLOBALS['app'] = $app;
