<?php
## define("DIR_PATH", "D://boilerplates/REST/");
define("DIR_PATH", dirname(__FILE__) . "/");

## Define Common Dirs
define("SYSTEM_DIR",    DIR_PATH . "system/");
define("ENGINE_DIR",    SYSTEM_DIR . "engine/");
define("LIBRARY_DIR",   SYSTEM_DIR . "library/");
define("MODEL_DIR",     SYSTEM_DIR . "model/");
define("VENDOR_DIR",    SYSTEM_DIR . "vendor/");
define("DB_DIR",        DIR_PATH . "database/");

## Lookup folder for api call
define("API_PATH",      DIR_PATH . "api/");

## Database Config
define("USER_NAME",     "root");
define("PASSWORD",      "123456789");
define("DATABASE",      "db_blog");
define("DB_PORT",       "3306");
define("DB_NAME",       "rest_api");
define("DB_PREFIX",     "");

## For MVC Model
define ("BASE_CONTROLLER",  'api/controller/');
define ("BASE_VIEW",        "api/view/" );
define ("BASE_MODEL",       "api/model/");


## API MICROSERVICE
define("SERVICE_API_KEY", "ABSD9810324000");

## KEY ENV
define('APPLICATION', 1);
define('APPLICATION_TEST', 0);
define('OPTION_HTTP_MAX_SIZE_SUPPORT', 10485760); // Max 10Mb 