<?php
namespace Http;

class DataSubmit {
    private $data = [];

    function __construct()
    {
        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST': 
                $this->data['POST'] = $_POST;
                $this->data['GET']  = $_GET;
                break;
            case 'PUT':
                $this->data['GET']  = $_GET;
            case 'GET':
                $this->data['GET'] = $_GET;   
                break; 
            case 'DELETE':
                $this->data['GET'] = $_GET;    
                break;         
        }
    }

    public function data() {
        return $this->data;
    }

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

}