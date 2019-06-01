<?php 
 /**
  * APPLICATION BOOTSTRAP
  * 1. Require config
  * 2. Require engine
  * 3. Require injection: Router, Model, View, Controller
  * 4. Require Router performance and load input.
  */

  use Engine\App\App as Application;

  ## load config
  if (file_exists(dirname(__FILE__, 2) . '/config.php')) {
      require_once dirname(__FILE__, 2) . '/config.php';
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
  $app->bootstrap(dirname(__FILE__) . './launch.php');
  
  ## Save app for implementation
  $GLOBALS['app'] = $app;



  








  