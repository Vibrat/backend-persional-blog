<?php
/**
 * API for File Uploader
 * 
 * @return 
 *  - Code 401: Unauthenticated
 *  - Code 302: Forbidden
 *  - Code 404: Not found
 */

use \System\Model\Controller;

/**
 * PublicController for File Uploader 
 * 
 * @access public
 * @extends \System\Model\Controller
 */
class PublicController extends Controller {

    /**
     * API - alive
     * 
     * @endpoint GET api=file/public/alive?token=<>
     */
    public function alive() {
        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->json->sendBack([
                'success'   => true,
                'code'      => 200,
                'message'   => 'This api is ready to use'
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 403,
            'message'   => 'Forbidden'
        ]);
    }
} 