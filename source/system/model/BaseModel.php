<?php
/**
 * Model Extension
 */
abstract class BaseModel {
    
    private $deps = [];

    ## Init BaseModel
    function __construct($deps) {
        
        ## load dependencies
        foreach($deps as $key => $value) {
            $this->deps[$key] = $value;
        }
    }

    /**
     * get a property
     * 
     * @param String $name db
     */
    function __get ($name) {
        return (isset($this->$name) ? $this->$name : $this->deps[$name]);
    }
}