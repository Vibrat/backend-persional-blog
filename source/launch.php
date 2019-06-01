<?php
## ENGINE FOR BOOTSTRAPPING

use JsonStore\JsonStore;
use System\Model\Model;
use System\Model\RestView;
use Authenticator\Authenticator;
use Token\Token;
use Vendor\Apier\Apier;
use beFunc\beFunc as Befunc;
use Http\DataSubmit;

session_start([
    'cookie_lifetime' => 86400,
]);

## start our engine
$this->set('engine', new Engine());
$engine = $this->get('engine');

## init Router
$engine->set('router', 
    new Router(
        BASE_CONTROLLER, 
        (isset($_GET['api']) ? $_GET['api'] = BeFunc::reBuildUrlSlash($_GET['api']) : ''), 
        DIR_PATH));

## init Json Package
$engine->set('json', new JsonStore());        

## init MySqli
$engine->set('db', 
    new MySqliDatabase(
        [
            'username' => USER_NAME,
            'password' => PASSWORD,
            'db'       => DB_NAME,
            'url'      => DATABASE,
            'port'     => DB_PORT    
        ]
    ));

## init architecture
$engine->set('model', new Model(['db' => $engine->db]));
$engine->set('view', new RestView());

## init Authenticator and TokenGenerator
$engine->set(
    'user', 
    new Authenticator($engine->db, new Token()));

## init api hanlder
$engine->set('apier',new Apier());
$engine->apier->setHeaders([
    'APIKEY: ' . SERVICE_API_KEY,
    'Content-Type: application/json',
]);

$engine->set('http', new DataSubmit());

## Load and inject dependencies  
$engine->router->load(
    $engine->router->getLoadFiles(), 
    [
        'db'        => $engine->db,
        'json'      => $engine->json,
        'model'     => $engine->model,
        'view'      => $engine->view,
        'user'      => $engine->user,
        'apier'     => $engine->apier,
        'http'      => $engine->http
    ]);
