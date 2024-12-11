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

    public function createAvatarFile()
    {
        $target_dir = UPLOAD_DIR;
        $target_file = $target_dir . basename($_FILES['Avatar']['name']);
        $target_file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        //check if file exist or move to another sub dir
        if ($_FILES['Avatar']['size'] >= 5000000) {
            return FILE_SIZE_ERROR;
        }

        if(!in_array($target_file_ext,$allowed_extensions)){
            return FILE_EXT_ERROR;
        }

        //if sql request success
        //move_uploaded_file(from: ,to: )

    }
    public function createFile() {}
    public function readFile() {}
    public function updateFile() {}
    public function deleteFile() {}
}
