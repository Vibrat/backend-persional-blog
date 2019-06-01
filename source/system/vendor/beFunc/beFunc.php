<?php
namespace beFunc;

/**
 * beFunc Class contains programming function 
 * that make you better developer
 */

class beFunc {

    public function array_combine( Array $keysArray, Array $valuesArray): Array {
        
        ## result representative
        $data = [];
        
        ## getting keys
        $keysValue =  array_keys($valuesArray);
        ## assign value
        foreach ($keysArray as $index => $value) {
            if (isset($keysValue[$index])) {
                $data[$value] = $valuesArray[$keysValue[$index]];
            }
        }

        ## return data
        return $data;
    }   

    public function reBuildUrlSlash(String $url) {
        $data = '';
        $split_texts = preg_split("/[A-Z]/", $url);
        preg_match_all("/[A-Z]/", $url, $matches);

        foreach($split_texts as $key => $text) {
            if ($key) {
                $data .= '-' . lcfirst($matches[0][$key - 1]) . $text;
            } else {
                $data .= $text;
            }
        }

        return $data;
    }
    
}