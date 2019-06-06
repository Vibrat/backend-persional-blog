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
                parse_str(
                    file_get_contents(
                        'php://input', 
                        false , 
                        null, 
                        0, 
                        $_SERVER['CONTENT_LENGTH'] ), 
                    $this->data['PUT']);
            case 'GET':
                $this->data['GET'] = $_GET;   
                break; 
            case 'DELETE':
                $this->data['GET'] = $_GET;
                parse_str(
                    file_get_contents(
                        'php://input', 
                        false , 
                        null, 
                        0, 
                        $_SERVER['CONTENT_LENGTH'] ), 
                    $this->data['DELETE']);    
                break;         
        }
    }

    public function data($name) {
        return $this->data[$name];
    }

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

}