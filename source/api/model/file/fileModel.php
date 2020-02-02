<?php

use \System\Model\BaseModel;

/**
 * Model for File API
 *
 */

class FileModel extends BaseModel {

    /**
     * Add new record into table `file`
     *
     * @param string filename
     * @param string path
     */
    public function addFileRecord($data) {
        $sql = "INSERT INTO `file` (`filename`, `path`) VALUES (:filename, :path)";
        $query = $this->db->query($sql, [
            ':filename'     => $data['filename'],
            ':path'         => $data['path']
        ]);

        return $query->rowsCount();
    }

    /**
     * Count Record in table `file`
     *
     * @param string filename
     * @return number 0 | 1
     */
    public function checkFileExist($filename) {
        $sql = "SELECT COUNT(*) AS total FROM `file` WHERE `filename` = :filename LIMIT 1";
        $query = $this->db->query($sql, [
            ':filename'  => $filename
        ]);
        return $query->row('total');
    }

    /**
     * Delete a record
     *
     * @param string filename
     */
    public function deleteFileRecord($filename) {
        $sql = "DELETE FROM `file` WHERE `filename` = :filename LIMIT 1";
        $query = $this->db->query($sql, [
            ':filename'     => $filename
        ]);

        return $query->rowsCount();
    }
}
