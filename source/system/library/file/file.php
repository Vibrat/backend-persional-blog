<?php
/**
 * Interface for File Handler on HTTP
 * 
 */
class HttpFileHandler {
    private $allowed_types = [];
    private $allowed_size  = null;

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

        if ($file['error']) {
            return [
                'success'   => false,
                'message'   => $file['error']
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