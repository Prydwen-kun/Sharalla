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

        $target_directory = $target_dir . 'Users/' . $user_id . '/';
        $target_file = $target_dir . 'Users/' . $user_id . '/Avatar' . $user_id . '.' . $target_file_ext;

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tiff', 'svg'];

        //check if dir exist
        if (!is_dir($target_directory)) {
            mkdir($target_directory, 0777, true);
        }

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

        //req check if ext exist in ext table
        $sql = 'INSERT INTO extension(label) 
                VALUES(:label)
                ON DUPLICATE KEY UPDATE id = id';

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('label', $target_file_ext, PDO::PARAM_STR);
                if (!$this->_req->execute()) {
                    return REQ_ERROR;
                }
            } else {
                return REQ_ERROR;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        //if not create new insert

        //check MIME broader type and insert new type if not already existing
        $file_type = explode('/', $fileMimeType)[0];

        //for this to work column need to have unique constraint
        $sql = 'INSERT INTO content_types(label) 
        VALUES(:label)
        ON DUPLICATE KEY UPDATE id = id';

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('label', $file_type, PDO::PARAM_STR);
                if (!$this->_req->execute()) {
                    return REQ_ERROR;
                }
            } else {
                return REQ_ERROR;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        //insert file table
        $sql =
            'INSERT INTO files(id, title, description, size, path,upload_date, uploader_id, extension_id, type_id)
            VALUES(
                DEFAULT,
                :title,
                :description,
                :size,
                :path,
                DEFAULT,
                :uploader_id,
                (SELECT extension.id FROM extension WHERE label =:ext_label),
                (SELECT content_types.id FROM content_types WHERE label =:type_label)
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
                $this->_req->bindParam('ext_label', $target_file_ext, PDO::PARAM_STR);
                $this->_req->bindParam('type_label', $file_type, PDO::PARAM_STR);

                if ($this->_req->execute()) {
                    return $target_file;
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

    public function getAllFiles($orderBy = 'id', $limit = 10)
    {
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;

        if (isset($_GET['search'])) {
            $search = htmlspecialchars($_GET['search']);
        } else {
            $search = '';
        }

        $keyword = '%' . $search . '%';

        switch ($orderBy) {
            case 'id':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY id
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'title':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY title
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'size':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY size
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'idDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY id DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'titleDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY title DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'sizeDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword
            ORDER BY size DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
        }
        return null;
    }
    public function getUserFileList($user_id, $orderBy = 'id', $limit = 10)
    {
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;

        if (isset($_GET['search'])) {
            $search = htmlspecialchars($_GET['search']);
        } else {
            $search = '';
        }

        $keyword = '%' . $search . '%';

        switch ($orderBy) {
            case 'id':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY id
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'title':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY title
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'size':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY size
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'idDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY id DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'titleDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY title DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
            case 'sizeDESC':
                $sql = "SELECT files.id AS id,
                files.title AS title,
                files.description AS description,
                files.size AS size,
                files.path AS path,
                files.upload_date AS upload_date,
                files.uploader_id AS uploader_id,
                users.username AS uploader,
                extension.label AS extension,
                content_types.label AS type
            FROM files
            LEFT JOIN users ON files.uploader_id = users.id
            JOIN extension ON files.extension_id = extension.id
            JOIN content_types ON files.type_id = content_types.id
            WHERE title LIKE :keyword AND uploader_id =:user_id
            ORDER BY size DESC
            LIMIT :list_limit OFFSET :offset";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
                        $this->_req->bindParam('offset', $offset, PDO::PARAM_INT);
                        $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                        if ($this->_req->execute()) {
                            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                            return $data;
                        }
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                break;
        }
        return null;
    }
    public function createFile() {}
    public function readFile() {}
    public function updateFile() {}
    public function countEntries()
    {
        try {
            $sql = "SELECT COUNT(*) AS entries FROM files";
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data['entries'];
                }
                return REQ_PREP_ERROR;
            }
            return REQ_ERROR;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function getUploaderId($file_id)
    {
        try {
            $sql = "SELECT files.uploader_id AS uploader_id
                    FROM files
                    WHERE files.id =:file_id";

            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('file_id', $file_id, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data['uploader_id'];
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function deleteFile($file_id)
    {
        //check in controller
        $sql = "DELETE FROM file_tags WHERE file_tags.file_id =:file_id;
                DELETE FROM liked_content WHERE liked_content.file_id =:file_id;
                DELETE FROM bookmarks WHERE bookmarks.file_id =:file_id;
                DELETE FROM comments WHERE comments.file_id =:file_id;
                DELETE FROM files WHERE files.id =:file_id;";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('file_id', $file_id, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    return RETURN_OK;
                }
                return REQ_ERROR;
            }
            return REQ_PREP_ERROR;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}
