<?php

/**
 * Default handler for Router
 * 
 * @method index return json errors
 */

use System\Model\Controller;

class DefaultRouter extends Controller {
    
    public function index() {
        $this->json->sendBack([
            'error'  => 'Class',
            'message'=> 'Class not found'
        ]);
    }
}