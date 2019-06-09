<?php
namespace Http;

class DataSubmit {
    private $data = [];
    private $http_response = [];

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
        $headers = [];

        foreach($data_lines as $line) {
            if (!empty($line)) {
                [$raw_key, $value] = preg_split("/\\r\\n\\r\\n/", $line);
                preg_match_all('/(?:;|\r\n|\s)?([a-zA-Z\s\.\d\"\/=:-]*)(?:;\s)?/', $raw_key, $headers);
                if (!empty($headers)  && count($headers) >= 2) {
                    array_shift($headers);
                    ## check if this is a file request
                    array_walk_recursive($headers, array($this, 'get_data_from_http'), $value);
                }
            }
        }

        return $data;
    }

    function get_data_from_http(&$header, &$key, $value) {
        if (preg_match("/^filename=/", $header) && !empty($header)) {
            preg_match("/(?:\")(.*)(?:\")/", $header, $raw_filename);
            preg_match("/(?:Content-Type:\s)(.*)/", $header, $raw_content_type);
            preg_match("/(?:\")(.*)(?:\")/", $header, $raw_key);
        } else {
            preg_match("/(?:\")(.*)(?:\")/", $header, $raw_key);
        }

        if (!empty($raw_key)) {
            $this->http_response[$raw_key[1]] = [
                'key' => $raw_key[1] ? $raw_key[1] : null,
                'value' => $value,
                'filename' => !empty($raw_filename) && $raw_filename[1] ? $raw_filename[1]: null,
                'Content-Type' => !empty($raw_content_type) && $raw_content_type[1] ? $raw_content_type[1] : null
            ];
        }
    }   
}