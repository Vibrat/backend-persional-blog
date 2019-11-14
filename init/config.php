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
define("DEPLOYMENT_SERVER", getenv('SERVER_NAME'));
define("USER_NAME",   DEPLOYMENT_SERVER ? getenv("DATABASE_USER") : "root");
define("PASSWORD",    DEPLOYMENT_SERVER ? getenv("DATABASE_PASS") :  "123456789");
define("DATABASE",    DEPLOYMENT_SERVER ? getenv("DATABASE_URL")  : "db_blog");
define("DB_PORT",       "3306");
define("DB_NAME",     DEPLOYMENT_SERVER ? getenv("DATABASE_NAME") : "rest_api");
define("DB_PREFIX",     "");

## For MVC Model
define ("BASE_CONTROLLER",  'api/controller/');
define ("BASE_VIEW",        "api/view/" );
define ("BASE_MODEL",       "api/model/");


## API MICROSERVICE
define("SERVICE_API_KEY", "ABSD9810324000");

## DEFAULT ACCOUNT
define("ROOT_PASSWORD", password_hash('TheDreamer', PASSWORD_BCRYPT));


define('DB_INTERVAL_CHECK', 5);
define('DB_NUM_TRIALS', 100);