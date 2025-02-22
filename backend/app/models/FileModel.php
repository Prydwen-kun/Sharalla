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

    public function getUserFileCount(int $user_id)
    {
        if (isset($_GET['search'])) {
            $search = htmlspecialchars($_GET['search']);
        } else {
            $search = '';
        }

        $keyword = '%' . $search . '%';

        $sql = "SELECT COUNT(files.title) AS results
        FROM files
        WHERE title LIKE :keyword AND files.uploader_id =:user_id";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data['results'];
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getResultsCount()
    {
        if (isset($_GET['search'])) {
            $search = htmlspecialchars($_GET['search']);
        } else {
            $search = '';
        }

        $keyword = '%' . $search . '%';

        $sql = "SELECT COUNT(files.title) AS results
        FROM files
        WHERE title LIKE :keyword";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data['results'];
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
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
    public function createFile(int $user_id, string $title_post = '')
    {
        //CHECK UPLOAD INTEGRITY
        if ($_FILES['Upload']['error'] != UPLOAD_ERR_OK) {
            return FILE_UPLOAD_ERROR;
        }

        if (isset($_POST['Description']) && !empty($_POST['Description'])) {
            $description = $_POST['Description'];
        } else {
            $description = 'No description...';
        }

        //TEST TITLE AND DESC SIZE
        if (strlen($title_post) > 255) {
            return FILE_TITLE_SIZE_ERR;
        }

        if (strlen($description) > 2000) {
            return FILE_DESC_SIZE_ERR;
        }

        $target_dir = UPLOAD_DIR;
        $upload_name = $_FILES['Upload']['name'];
        $target_file_ext = strtolower(pathinfo($upload_name, PATHINFO_EXTENSION));

        $target_directory = $target_dir . 'Files/' . $user_id . '/';

        $title = $title_post;

        if ($title === '') {
            //if file exist rand title and retest
            do {
                $rand_title = rand(1000, 99999999);
                $target_file = $target_dir . 'Files/' . $user_id . '/' . $rand_title . '.' . $target_file_ext;
                $title = $rand_title;
            } while (file_exists($target_file));
        } else {
            $target_file = $target_dir . 'Files/' . $user_id . '/' . $title . '.' . $target_file_ext;
            if (file_exists($target_file)) {
                do {
                    $rand_title = rand(1000, 99999999);
                    $target_file = $target_dir . 'Files/' . $user_id . '/' . $title . $rand_title . '.' . $target_file_ext;
                    $title = $rand_title;
                } while (file_exists($target_file));
            }
        }

        //check if dir exist
        if (!is_dir($target_directory)) {
            mkdir($target_directory, 0777, true);
        }

        //replace file since it's supposed to be unique
        //test size info first
        if ($_FILES['Upload']['size'] >= _2GB_UPLOAD_LIMIT) {
            return FILE_SIZE_ERROR;
        }

        //test tmp file size second
        $temp_filepath = $_FILES['Upload']['tmp_name'];
        $fileSize = filesize($temp_filepath);
        if ($fileSize === false || $fileSize >= _2GB_UPLOAD_LIMIT) {
            return FILE_SIZE_ERROR;
        }

        $fileMimeType = mime_content_type($temp_filepath);

        if ($fileMimeType === false) {
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
                $this->_req->bindParam('title', $title, PDO::PARAM_STR);
                $this->_req->bindParam('description', $description, PDO::PARAM_STR);
                $this->_req->bindParam('size', $fileSize, PDO::PARAM_INT);
                $this->_req->bindParam('path', $target_file, PDO::PARAM_STR);
                $this->_req->bindParam('uploader_id', $user_id, PDO::PARAM_STR);
                $this->_req->bindParam('ext_label', $target_file_ext, PDO::PARAM_STR);
                $this->_req->bindParam('type_label', $file_type, PDO::PARAM_STR);

                if ($this->_req->execute()) {
                    return RETURN_OK;
                }
                return REQ_ERROR;
            }
            return REQ_ERROR;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function readFile($file_id)
    {
        try {
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
                    JOIN users ON files.uploader_id = users.id
                    JOIN extension ON files.extension_id = extension.id
                    JOIN content_types ON files.type_id = content_types.id
                    WHERE files.id =:file_id
                    ";

            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('file_id', $file_id, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data;
                } else {
                    return REQ_PREP_ERROR;
                }
            } else {
                return REQ_ERROR;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
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

    public function getFilePathFromId($file_id)
    {
        try {
            $sql = "SELECT files.path AS file_path,
                    content_types.label AS file_type
                    FROM files
                    JOIN content_types ON files.type_id = content_types.id
                    WHERE files.id =:file_id";

            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('file_id', $file_id, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                    return $data;
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function downloadFile()
    {
        $file_id = $_GET['fileId'];
        $file_spec = $this->getFilePathFromId($file_id);

        if ($file_spec === false) {
            return false;
        }

        $file_path = $file_spec['file_path'];
        $file_type = $file_spec['file_type'];

        if (file_exists($file_path) && $file_type !== 'image') {
            $mime_type = mime_content_type($file_path);
            // Set headers to indicate a file download 
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            // Clear system output buffer
            ob_clean();
            flush();
            // Read the file and output its contents
            $handle = fopen($file_path, "rb");
            $file_content = fread($handle, filesize($file_path));
            fclose($handle);
            return $file_content;
        } else if (file_exists($file_path) && $file_type === 'image') {
            // Set the appropriate content-type header
            $mime_type = mime_content_type($file_path);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            // Clear system output buffer 
            ob_clean();
            flush();
            // Read the file and output its contents
            $handle = fopen($file_path, "rb");
            $file_content = fread($handle, filesize($file_path));
            fclose($handle);
            return $file_content;
        } else {
            return false;
        }
    }
}
