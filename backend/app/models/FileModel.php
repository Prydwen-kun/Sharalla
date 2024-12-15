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

    public function createAvatarFile(int $user_id = 0)
    {
        //CHECK UPLOAD INTEGRITY
        if ($_FILES['Avatar']['error'] != UPLOAD_ERR_OK) {
            return FILE_UPLOAD_ERROR;
        }

        $target_dir = UPLOAD_DIR;
        $upload_name = $_FILES['Avatar']['name'];
        $target_file_ext = strtolower(pathinfo($upload_name, PATHINFO_EXTENSION));

        $target_file = $target_dir . 'Users/' . $user_id . '/Avatar' . $user_id . $target_file_ext;

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tiff', 'svg'];

        //replace file since it's supposed to be unique
        //test size info first
        if ($_FILES['Avatar']['size'] >= 5000000) {
            return FILE_SIZE_ERROR;
        }

        //test tmp file size second
        $temp_filepath = $_FILES['Avatar']['tmp_name'];
        $fileSize = filesize($temp_filepath);
        if ($fileSize === false || $fileSize >= 5000000) {
            return FILE_SIZE_ERROR;
        }

        if (!in_array($target_file_ext, $allowed_extensions)) {
            return FILE_EXT_ERROR;
        }

        //verify mime-content type to check if file is an image
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/jpg', 'image/webp', 'image/tif', 'image/tiff', 'image/svg'];
        $fileMimeType = mime_content_type($temp_filepath);

        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return FILE_EXT_ERROR;
        }

        //Si toute la valid passe move_file =>
        move_uploaded_file($temp_filepath, $target_file);

        $sql = 'INSERT INTO files(id, title, description, size, path,upload_date, uploader_id, extension_id, type_id)
        VALUES(
        DEFAULT,
        :title,
        :description,
        :size,
        :path,
        DEFAULT,
        :uploader_id,
        :extension_id,
        :type_id
        )';

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                //bind param
                $title = 'Avatar' . $user_id;
                $description = 'Avatar of user ' . $user_id;

                $this->_req->bindParam('title', $title, PDO::PARAM_STR);
                $this->_req->bindParam('description', $description, PDO::PARAM_STR);
                $this->_req->bindParam('size', $fileSize, PDO::PARAM_INT);
                $this->_req->bindParam('path', $target_file, PDO::PARAM_STR);
                $this->_req->bindParam('uploader_id', $user_id, PDO::PARAM_STR);

                if ($this->_req->execute()) {
                    return RETURN_OK;
                }
                return REQ_ERROR;
            }
            return REQ_ERROR;
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        //if sql request success
        //move_uploaded_file(from: ,to: )

    }
    public function createFile() {}
    public function readFile() {}
    public function updateFile() {}
    public function deleteFile() {}
}
