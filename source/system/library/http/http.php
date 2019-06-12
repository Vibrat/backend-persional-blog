<?php
namespace Http;

class DataSubmit {
    private $data = [];
    private $http_response = [];

    function __construct()
    {
        ## Limit communication size with HTTP
        if  (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] >= OPTION_HTTP_MAX_SIZE_SUPPORT) {
            throw new \Exception('Communication package size is limited to 10 Mb');
        }

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

    public function data($name = null) {
        if ($name) 
            return $this->data[$name];

        return $this->data;
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
       
        parse_str(file_get_contents('php://input', false , null, 0, $_SERVER['CONTENT_LENGTH'] ), $data);
        $data_lines = preg_split("/\\r\\n----------------------------\d*-{0,2}\\r\\n(Content-Disposition: form-data; ){0,}/", array_shift($data));
        $headers = [];

        foreach($data_lines as $line) {
            if (!empty($line)) {
                [$raw_key, $value] = preg_split("/\\r\\n\\r\\n/", $line);
                preg_match_all('/(?:;|\r\n|\s)?([a-zA-Z\s\.\d\"\/=:\-_]+)(?:;\s)?/', $raw_key, $headers);
                if (!empty($headers)  && count($headers) >= 2) {
                    array_shift($headers);
                    ## check if this is a file request
                    array_walk($headers, array($this, 'get_data_from_http'), $value);
                }
            }
        }

        return $this->http_response;
    }

    private function get_data_from_http($header, $key, $value) {

        $header = array_filter($header, function ($item_val) {
            return !empty($item_val);
        });

        $data = [];
        foreach ($header as $row_data) {
            if (preg_match("/^filename=/", $row_data) && !empty($row_data)) {
                preg_match("/(?:\")(.*)(?:\")/", $row_data, $raw_filename);
                preg_match("/(?:Content-Type:\s)(.*)/", $row_data, $raw_content_type);
                $data = array_merge($data, [
                    'filename' => $raw_filename[1],
                    'Content-Type' => $raw_content_type[1]
                ]);
            } elseif (preg_match("/^(name=)|(\"[a-zA-Z\s\.\d\"\/=:\-_]*\"$)/", $row_data)){
                preg_match("/(?:\")([a-zA-Z\s\.\d\"\/=:\-_]*)(?:\")/", $row_data, $raw_key);
                $data['key'] = $raw_key[1];
                $data['value'] = $value;
            }
        }

        if (isset($data['key'])) {
            $this->http_response[$data['key']] = $data['value'];
        }
    } 
}