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
                $this->data['PUT']  = $this->parse_data(); 
                break;
            case 'GET':
                $this->data['GET'] = $_GET;   
                break; 
            case 'DELETE':
                $this->data['GET'] = $_GET;
                $this->data['DELETE']  = $this->parse_data(); 
                break;         
        }
    }

    public function data($name) {
        return $this->data[$name];
    }

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Parse response getting from HTTP Header which supports form-data type
     * This method only need to run in method DELETE and PUT 
     * 
     * 
     * @important this does not support file type yet
     */
    private function parse_data() {
        $data = [];
        ## parse data from HTTP Header
        parse_str(file_get_contents('php://input', false , null, 0, $_SERVER['CONTENT_LENGTH'] ), $data);
        var_dump($data);
        $data_lines = preg_split("/\\r\\n----------------------------\d*-{0,2}\\r\\n(Content-Disposition: form-data; ){0,}/", array_shift($data));
        foreach($data_lines as $line) {
            if (!empty($line)) {
                $headers = [];
                [$key, $value] = preg_split("/\\r\\n\\r\\n/", $line);
                // preg_match_all('/(?:")([a-zA-Z\s\.\d]*?)(?:")/', $key, $key);
                preg_match_all("/(?:;?)([a-zA-Z\s\.\d\"\/=:-]*)(?:;*|(?:\\r\\n)?)/", $key, $headers);
                if (!empty($headers)) {

                }
            }
        }

        return $data;
    }
    
}