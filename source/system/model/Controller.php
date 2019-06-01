<?php 
namespace System\Model;

/**
 * Controller Model - MVC Model
 * Use as extention
 */
abstract class Controller {
    
    /**
     * Dependencies
     */
    private $actions = [];

    /**
     * Magic function
     */
    function __construct($dependencies) {
        $this->actions = array_merge($this->actions, $dependencies);
    }

    /**
     * Set dependencies
     * 
     * @param String $name Name of dependency
     * @param Class $object Instance of an class
     */
    public function set($name, $object) {
        $this->actions[$name] = $object;
    }

    public function __get($name) {
        return $this->actions[$name];
    }

    /**
     * Is called if index function does not exist
     */
    public function error() {
        
        $this->json->sendBack([
            'error'     => 'Method',
            'message'   => 'Class found while method does not exist'
        ]);
    }   
}