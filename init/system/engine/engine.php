<?php
/**
 * API REST BOOTSTRAPPING
 */

class Engine {
    private $childs = [];

    function __construct() {}

    /**
     * @function inject
     * @param string $name
     * @param class $object
     * Used in engine to inject class in startup
     */

    public function set($name, $object) {
        if (!isset($this->childs[$name])) {
           return  $this->childs[$name] = $object;
        } else {
            trigger_error('injected object ' . $name . ' is already exists in Engine', E_USER_ERROR );
        }

        return false;
    }

    /**
     * @function __get magic function
     * @param string $name name of object set at the startup
     */
    public function __get($name) {
        return $this->childs[$name];
    }
     
    /**
     * Return dependencies
     * 
     * @return Array
     */
    public function getDependencies() {
        return $this->childs;
    }
 }