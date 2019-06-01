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
     * Index function
     * 
     * is called if not method is specified in Url
     */

    public function index() {
        # Perform index here
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
     * Perform error when no action is called (when function index() not exists)
     */
    public function error() {
        ## in the future we do something here
    }
    
}