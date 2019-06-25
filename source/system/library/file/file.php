<?php
/**
 * Interface for File Handler on HTTP
 * 
 */
class HttpFileHandler {
    private $allowed_types = [];
    private $allowed_size  = null;
    private $errors = [
        '1'     => 'file size exceeds allowed size',
        '2'     => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        '3'     => 'The uploaded file was only partially uploaded.',
        '4'     => 'No file was uploaded.',
        '6'     => 'Missing a temporary folder',
        '7'     => 'Failed to write file to disk',
        '8'     => ' PHP extension stopped the file upload'  
    ];

    /**
     * Add allowed extensions
     * 
     * @see https://en.wikipedia.org/wiki/Media_type#Type_application
     */
    public function allow_types(Array $allowed_types, Number $max_size = null) {
        $this->allowed_types = $allowed_types;
        $this->allowed_size = $max_size;
    }

    /**
     * Validate file
     * 
     *  - Validate file type
     *  - Validate file size
     *  - validate additional errors, see https://www.php.net/manual/en/features.file-upload.errors.php
     * @param $file
     * @return $file
     */
    public function validate($file) {
        
        if ($file['error']) {
            return [
                'success'   => false,
                'message'   => $this->errors[$file['error']]
            ];
        }

        if (!in_array($file['type'], $this->allowed_types)) {
            return [
                'success'   => false,
                'message'   => 'Invalid Type'
            ];
        }

        if ($this->allowed_size && $file['size'] > $this->allowed_size) {
            return [
                'success'   => false,
                'message'   => 'file size exceeds permitted value'
            ];
        }

        return $file;
    }

    /**
     * Move file 
     * 
     * @param string $file
     * @param string $to contains filename
     * @return boolean
     */
    public function move(Array $file, String $to) {
        return move_uploaded_file($file['tmp_name'], $to);
    }
}