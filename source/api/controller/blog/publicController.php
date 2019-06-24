<?php

/**
 * Public API for Blog API
 * 
 */

 use \System\Model\Controller;

 class PublicController extends Controller {

    /**
     * Check if this api is active
     * 
     * @endpoint GET api=blog/public/enablement&token=<>
     */
    public function enablement() {

        $get_data = $this->http->data('GET');
        if($this->user->isTokenValid($get_data['token'])) {
            
            $this->json->sendBack([
                'success'   => true,
                'code'      => 200,
                'message'   => 'Service is alive'
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 403,
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * Create new blog record
     * 
     * @endpoint POST api=blog/public/create&token=<>
     * @param string title - required - unique value 
     * @param string des - optional 
     * @param string tags - optional
     * @param string category - optional
     * @param string seo_title - required - unique value
     * @param string seo_des - optional
     * @param string seo_url - required - unique value
     */
    public function create() {
        if ($this->http->method() != 'POST') {
            
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method POST'
            ]);
            return;
        }

        $get_data = $this->htt->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
    }
 }
