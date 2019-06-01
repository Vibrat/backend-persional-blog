<?php 
namespace JsonStore;

/**
 * Json Object for handling API Response
 * 
 */

 class JsonStore {
    private $env;
    /**
     * Default init to return JSON
     */
    function __construct(int $env = APPLICATION) {
        $this->env = $env;
        header('Content-type:application/json;charset=utf-8');
    }

    /**
     * Set Custom Headers
     * 
     * @param String $header
     */
    public function setHeaders($header) {
        header($header);
    }

    /**
     * Return Json API
     * 
     * @param Array $array
     */
    public function sendBack($data) {
        if ($this->env) {
            echo json_encode($data);
        } 
    }

    /**
     * Encode $array to json
     * 
     * @param Array $data
     */
    public function json_encode($data) {
        return json_encode($data);
    }

    /**
     * Decode Json into Array
     * 
     * @param Json $data
     */
    public function json_decode($data) {
        return json_Decode($data);
    }
 }