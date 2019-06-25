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

    /**
     * Upload a file
     * 
     * @endpoing POST api=file/public/upload&token=<>
     * @param string name - name of file to be changed to
     * @param File file file that is uploaded
     */
    public function upload() {

        if ($this->http->method() != 'POST') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method POST'
            ]);
            return;
        }

        $get_data  = $this->http->data('GET');
        $post_data = $this->http->data('POST');

        if ($this->user->isTokenValid($get_data['token'])) {
            $this->file->allow_types([
                'application/pdf',
                'text/css',
                'text/html',
                'text/xml',
                'text/csv',
                'text/txt',
                'text/plain',
                'image/png',
                'image/jpeg',
                'image/gif',
                'image/jpg'
            ]);

            $validated_file = $this->file->validate($_FILES['file']);
            if (isset($validated_file['success']) && !$validated_file['success']) {
                $this->json->sendBack([
                    'success'   => false,
                    'message'   => $validated_file['message']
                ]);
                return;
            }

            try {
                $this->model->load("file/file");
                $filename = (isset($post_data['name']) ? $post_data['name'] : $validated_file['name']);
                $new_location = STORAGE_API . $filename;
                $is_not_file = $this->model->file->checkFileExist($filename); 
                if (!$is_not_file) {
                    $this->file->move($validated_file, $new_location);                  
                    $validated_file['location'] = $new_location;
                    $this->model->file->addFileRecord([
                        'filename'  => $filename,
                        'path'      => $new_location
                    ]);
                    
                    $this->json->sendBack([
                        'success'   => true,
                        'data'      => $validated_file
                    ]);
                    return;
                }
                
                $this->json->sendBack([
                    'success'   => false,
                    'message'   => 'file already exists in server'
                ]);
                return;
            } catch (\Exception $e) {
                $this->json->sendBack([
                    'success'   => false,
                    'message'   => $e->getMessage()
                ]);
                return;
            }
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * Delete a file record
     * 
     * @param string $filename
     */
    public function delete() {

        if ($this->http->method() !== 'DELETE') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only support method DELETE'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('file/file');
            $is_file = $this->model->file->checkFileExist($get_data['filename']);
            if ($is_file && unlink(STORAGE_API . $get_data['filename'])) {
               $this->model->file->deleteFileRecord($get_data['filename']);
               $this->json->sendBack([
                    'success'   => true,
                    'code'      => 200,
                    'message'   => 'Successfully delete a file'
               ]);
               return;
            }

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'file does not exist to delete'
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,    
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * Provide way to access to image resource
     * 
     * https://www.php.net/manual/en/function.imagecreatefrompng.php
     * @endpoint GET api=file/file/get&width=<>&height=<>
     * @return image 
     */
    public function get() {

        // Check method 
        if ($this->http->method() != 'GET') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only support method GET'
            ]);
            return;
        }
        
        $this->model->load('file/file');
        $get_data  = $this->http->data('GET');
        $filename  = $get_data['filename'];

        if ($this->model->file->checkFileExist($filename)) {
            
            // factorize image
            $original_img = $this->imageFactory(STORAGE_API, $filename);
            
            $modified_width  = isset($get_data['width']) ? (int) $get_data['width'] : $original_img['width'];
            $modified_height = isset($get_data['height']) ? (int) $get_data['height'] : $original_img['height'];
            $modified_img = imagecreatetruecolor($modified_width, $modified_height);

            if ($original_img) {
                
                // Output resized image
                header('Content-Type: ' . $original_img['extension']);
                imagecopyresized($modified_img, $original_img['image'], 0, 0, 0, 0, $modified_width, $modified_height, $original_img['width'], $original_img['height']);
                str_replace("/", "", $original_img['extension'])($modified_img);
            } else { 

                // return Notfound image
                $font_path = dirname(__FILE__) . "/_font/Roboto-Black.ttf"; // font for NotFound image
                $black = imagecolorallocate($modified_img, 0, 0, 0);
                $white = imagecolorallocate($modified_img, 255, 255, 255);

                imagefill($modified_img, 0, 0, $white);
                imagefttext($modified_img, 20, 0, ($modified_width -20*(strlen('Not found')))/2 , $modified_height/2 + 10, $black, $font_path, 'Not found');

                header('Content-Type: image/jpeg');
                imagejpeg($modified_img);
            }

            imagedestroy($modified_img);
            imagedestroy($original_img);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'message'   => 'Image does exist in server'
        ]);
    }

    /**
     * Factory to produce image
     * 
     * @param string $path - a link to directory
     * @param string filename - filename in an directory
     * @return array | boolean
     */
    private function imageFactory($path, $filename) {
        $original_img = false;
        $extension    = mime_content_type($path . $filename); // Return true extension of a file

        switch($extension) {
            case 'image/png': 

                $original_img = imagecreatefrompng($path . $filename);
                [$width, $height, $others] = getimagesize($path . $filename);
                
                break;
            case 'image/jpeg':

                $original_img = imagecreatefromjpeg($path . $filename);
                [$width, $height, $others] = getimagesize($path . $filename);
                
                break;
        }

        $data = [
            'name'          => $filename,
            'extension'     => $extension, 
            'image'         => $original_img,
            'width'         => $width,
            'height'        => $height    
        ];

        return (!in_array(false, $data) ? $data : false);
    }
} 