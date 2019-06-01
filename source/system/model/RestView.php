<?php 
namespace System\Model;

/**
 * VIEW - CORE ENGINE
 * 
 * use for core engine
 */
class RestView {
    
    /**
     * @function setOutput
     * @param $html string
     * output HTML into DOM
     */

    public function setOutPut($html) {
        echo $html;
    } 

    public function setJson($code, $message, $json) {
        $response = [
            'status' => $code,
            'status_message' => $message,
            'data' => $json
        ];
        
        echo json_encode($response);
    }
 }