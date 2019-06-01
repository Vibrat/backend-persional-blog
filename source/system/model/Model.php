<?php
namespace System\Model;

/**
 * MODEL CLASS - CORE ENGINE
 * 
 * user for engine not extension
 */
final class Model {
    protected $db;
    /**
     * Dependencies contain classes
     */
    protected $deps;

    function __construct(Array $deps) {
        foreach ($deps as $key => $value) {
            $this->deps[$key] = $value;
        }
    }
    
    /**
     * Inject dependencies
     * 
     * @param class $object
     * inject a object into Model
     */
    private function inject($name, $object) {
        $this->$name = $object;
    }

    /**
     * magic function to refer to object in Model
     */
    function __get($name) {
        return (isset($this->$name) ? $this->$name : $this->deps[$name]);
    }

    /**
     * Load a resource in Model
     */
    public function load($path) {
        ## revise base url
        $items =  preg_split("/[\/\\\]/", $path);

        ## init value and identifier  
        $key = $items[count($items) - 1];
        $path = BASE_MODEL . $path . "Model.php";
        $className = ucwords($key . "Model");
        
        if(file_exists($path)) {
            include_once $path;

            ## inject for using in Controller
            $this->inject($key, new $className($this->deps));

            return;
        }
            
        trigger_error('file does not exist in path' . $path, E_USER_ERROR);
    }
 }