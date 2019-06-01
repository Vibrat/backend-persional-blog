<?php
namespace Engine\App;

class App {

    ## engine
    private $engine;

    ## function to bootstrap application
    public function bootstrap($bootstrapFile) {
        require_once $bootstrapFile;
    }

    ## set value to App Object
    public function set($name, $object) {
        $this->$name = $object;
    }

    ## get method
    public function get($name) {
        return $this->$name;
    }

    ## get magic method
    function __get($name) {
        return $this->$name;
    }
}