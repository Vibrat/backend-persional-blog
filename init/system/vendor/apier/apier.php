<?php
namespace Vendor\Apier;

/**
 * Apier Class used for call APIs
 * 
 * Design by https://www.weichieprojects.com/blog/curl-api-calls-with-php/
 */
class Apier {

    /**
     * Header in API CALL
     */
    private $headers = [];

    /**
     * Call an API
     * 
     * @param String POST | PUT | GET | DELETE
     * @param String $url
     * @param Array $data
     * @return Response
     */
    public function call($method, $url, $data = false) 
    {
        ## init curl
        $curl = curl_init();

        ## setting options  
        if ($this->headers && $method == 'GET') {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }

        ## set data and method
        switch ($method) {
            case 'GET': 
                curl_setopt($curl, CURLOPT_HTTPGET, 1);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;  
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;			 					
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
        }

        ## set url and config
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        ## comming port
        curl_setopt($curl, CURLOPT_PORT , 80);

        ## execute API
        $result = curl_exec($curl);
    
        ## if API call fails
        if(!$result) {
            die (curl_error($curl));
        }
       
        ## close and return response
        curl_close($curl);        
        return json_decode($result);
    }

    /**
     * Config header
     * 
     * @param Array $header
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
    }
}