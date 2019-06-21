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
                'image/gif'
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
} 