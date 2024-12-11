<?php
class FileModel extends CoreModel
{
    private $_req;
    private $_file;

    public function __destruct()
    {
        if (!empty($this->_req)) {
            $this->_req->closeCursor();
        }
    }

    public function createFile() {}
    public function readFile() {}
    public function updateFile() {}
    public function deleteFile() {}
}
