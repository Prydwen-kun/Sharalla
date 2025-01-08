<?php
class UserModel extends CoreModel
{
    private $_req;
    private $_user;

    public function __destruct()
    {
        if (!empty($this->_req)) {
            $this->_req->closeCursor();
        }
    }

    public function userExist($username)
    {
        $query = "SELECT * FROM users WHERE users.username =:username";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->bindValue("username", $username, PDO::PARAM_STR);
        $this->_req->execute();
        $response = $this->_req->fetchAll(PDO::FETCH_ASSOC);
        $this->_req->closeCursor();
        return count($response) == 1 ? true : false;
    }

    public function signup(int $rank, string $avatar)
    {
        if (!empty($_POST)) {
            $post = $_POST;
        } else {
            return POST_EMPTY;
        }

        if ($post['Password'] !== $post['PasswordConfirm']) {
            return PWD_CONFIRM_ERROR;
        }

        if (strlen($post['Username']) < 3 || strlen($post['Username']) > 255) {
            return USERNAME_LENGTH_ERROR;
        }

        if (strlen($post['Password']) < 8 || strlen($post['Password']) > 255) {
            return PWD_LENGTH_ERROR;
        }

        if (!filter_var($post['Email'], FILTER_VALIDATE_EMAIL)) {
            return EMAIL_ERROR;
        }

        $query = "INSERT INTO users(id,email,username,password,last_login,rank,avatar,signup_date,auth_token)
         VALUES(DEFAULT,:email,:username,:password,DEFAULT,:rank,:avatar,DEFAULT,NULL);
        INSERT INTO status 
        VALUES(DEFAULT,
        2,
        (SELECT users.id FROM users WHERE username=:username));";

        if (!$this->userExist($post['Username'])) {
            $this->_req = $this->getDb()->prepare($query);
            $this->_req->bindValue("username", $post['Username'], PDO::PARAM_STR);
            $this->_req->bindValue("email", $post['Email'], PDO::PARAM_STR);
            $this->_req->bindValue("password", password_hash($post['Password'], PASSWORD_BCRYPT), PDO::PARAM_STR);
            $this->_req->bindValue("rank", $rank, PDO::PARAM_INT);
            $this->_req->bindValue("avatar", $avatar, PDO::PARAM_STR);
            if ($this->_req->execute()) {
                return RETURN_OK;
            } else {
                return REQ_ERROR;
            }
        } else {
            return USERNAME_TAKEN_ERROR;
        }
    }

    public function login($username, $password)
    {

        $query = "SELECT users.id AS id,
        users.password AS password,
        users.email AS email,
        users.last_login AS last_login,
        ranks.label AS rank,
        ranks.power AS power 
        FROM users 
        JOIN ranks ON users.rank = ranks.id 
        WHERE username = :username";
        $this->_req = $this->getDb()->prepare($query);
        if (!$this->_req->execute(['username' => $username])) {
            return REQ_ERROR;
        }
        $this->_user = $this->_req->fetch(PDO::FETCH_ASSOC);
        $this->_req->closeCursor();

        if ($this->_user && password_verify($password, $this->_user['password'])) {
            if ($this->lastLoginUpdate($this->_user['id'])) {
                //generate token to client
                do {
                    $auth_token = generateToken();
                    $continue = $this->updateUserToken($auth_token, $this->_user['id']);
                    if ($continue === false) {
                        return REQ_ERROR;
                    }
                } while ($continue !== true);
                //repeat if key already in use
                if ($this->statusUpdate(STATUS_ONLINE, $this->_user['id'])) {
                    return [
                        'auth_token' => $auth_token
                    ];
                } else {
                    return REQ_ERROR;
                }
            } else {
                return REQ_ERROR;
            }
        } else {
            return CREDENTIALS_ERROR;
        }
    }
    public function updateUserToken($auth_token, $user_id)
    {
        $sql = "SELECT users.id 
        FROM users 
        WHERE users.auth_token =:auth_token";
        $this->_req = $this->getDb()->prepare($sql);
        $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
        $this->_req->execute();
        $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
        $this->_req->closeCursor();

        if (count($data) !== 0) {
            return -1;
        }

        $sql = "UPDATE users 
        SET users.auth_token =:auth_token
        WHERE users.id=:user_id";

        $this->_req = $this->getDb()->prepare($sql);
        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
        return $this->_req->execute();
    }
    public function getUserIdFromToken($auth_token)
    {
        $sql = "SELECT users.id as user_id
        FROM users WHERE users.auth_token =:auth_token";
        $this->_req = $this->getDb()->prepare($sql);
        $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
        if ($this->_req->execute()) {
            $data = $this->_req->fetch(PDO::FETCH_ASSOC);
            return $data['user_id'];
        } else {
            return false;
        }
    }

    public function getUserPowerFromToken($auth_token)
    { {
            $sql = "SELECT ranks.power as power
            FROM users
            JOIN ranks ON users.rank = ranks.id
            WHERE users.auth_token =:auth_token";
            $this->_req = $this->getDb()->prepare($sql);
            $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
            if ($this->_req->execute()) {
                $data = $this->_req->fetch(PDO::FETCH_ASSOC);
                return $data['power'];
            } else {
                return false;
            }
        }
    }
    public function statusUpdate($status_id, $user_id)
    {
        $query = "UPDATE status
        SET label_id = :status_id
        WHERE status.user_id =:user_id";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $this->_req->bindParam('status_id', $status_id, PDO::PARAM_INT);
        return $this->_req->execute();
    }
    public function getUserStatus($user_id)
    {
        $sql = "SELECT status_label.label AS status
        FROM status_label
        JOIN status ON status_label.id = status.label_id
        WHERE status.user_id =:user_id";

        $this->_req = $this->getDb()->prepare($sql);
        $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
        if ($this->_req->execute()) {
            return $this->_req->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
    public function lastLoginUpdate($user_id)
    {
        $query = "UPDATE users
        SET last_login = DEFAULT
        WHERE users.id = :user_id";
        $this->_req = $this->getDb()->prepare($query);
        return $this->_req->execute(['user_id' => $user_id]);
    }

    public function logout($auth_token)
    {
        $user_id = $this->getUserIdFromToken($auth_token);
        if ($user_id !== false) {
            $sql = "UPDATE users
            SET auth_token = NULL
            WHERE users.auth_token =:auth_token";
            $this->_req = $this->getDb()->prepare($sql);
            if ($this->_req->execute(['auth_token' => $auth_token])) {
                return $this->statusUpdate(STATUS_OFFLINE, $user_id);
            } else {
                return REQ_ERROR;
            }
        } else {
            return REQ_ERROR;
        }
    }

    public function isLoggedIn($auth_token)
    {
        if ($auth_token !== null) {
            $sql = "SELECT users.username AS username
                    FROM users
                    WHERE users.auth_token =:auth_token";
            $this->_req = $this->getDb()->prepare($sql);
            $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
            $this->_req->execute();
            $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
            $this->_req->closeCursor();
            return count($data) === 1 ? true : false;
        } else {
            return false;
        }
    }

    public function getCurrentUserId($auth_token)
    {
        $current_user_id = $this->getUserIdFromToken($auth_token);
        if ($current_user_id !== false && $current_user_id !== null) {
            return $current_user_id;
        } else {
            return false;
        }
    }

    public function getCurrentUserPower($auth_token)
    {
        $current_user_power = $this->getUserPowerFromToken($auth_token);
        if ($current_user_power !== false && $current_user_power !== null) {
            return $current_user_power;
        } else {
            return false;
        }
    }

    public function getCurrentUser($auth_token)
    {
        $query =
            "SELECT users.id AS id,
            users.username AS username,
            users.email AS email,
            users.last_login AS last_login,
            users.rank AS rank_id,
            ranks.label AS rank,
            ranks.power AS power,
            users.avatar AS avatar,
            users.signup_date AS signup_date
             FROM users
             LEFT JOIN ranks ON users.rank = ranks.id 
             WHERE users.auth_token = :auth_token";

        $this->_req = $this->getDb()->prepare($query);
        $this->_req->bindParam('auth_token', $auth_token, PDO::PARAM_STR);
        if ($this->_req->execute()) {
            return $this->_req->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function getUser($user_id, bool $is_admin = false)
    {
        if (!$is_admin) { //if profile private only admin should be able to access
            $query =
                "SELECT users.id AS id,
                username,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar,
                users.signup_date AS signup_date
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE users.id = :user_id";
            $this->_req = $this->getDb()->prepare($query);

            if ($this->_req->execute(['user_id' => $user_id])) {
                return $this->_req->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } else if ($is_admin) {
            $query =
                "SELECT users.id AS id,
                username,
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar,
                users.signup_date AS signup_date
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE users.id = :user_id";
            $this->_req = $this->getDb()->prepare($query);

            if ($this->_req->execute(['user_id' => $user_id])) {
                return $this->_req->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        }
    }

    public function getAllUser($orderBy = 'id', $limit = 10)
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
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
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
            case 'username':
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
            ORDER BY username
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
            case 'last_login':
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
            ORDER BY last_login
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
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
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
            case 'usernameDESC':
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
            ORDER BY username DESC
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
            case 'last_loginDESC':
                $sql = "SELECT users.id AS id,
                username,
                last_login,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE username LIKE :keyword
            ORDER BY last_login DESC
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

    public function getUsersFromKeyWord(string $search)
    {
        $keyword = '%' . $search . '%';
        $sql = "SELECT users.id AS id,
        username,
        last_login,
        users.avatar AS avatar
        FROM users
        LEFT JOIN ranks ON users.rank = ranks.id
        WHERE username LIKE :keyword
        ORDER BY username
        LIMIT 10";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('keyword', $keyword, PDO::PARAM_STR);
                if ($this->_req->execute()) {
                    $data = $this->_req->fetchAll(PDO::FETCH_ASSOC);
                    return $data;
                }
            } else {
                return null;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function banUser($user_id)
    {
        $sql = "UPDATE users
        SET users.rank = :ban_rank
        WHERE users.id =:user_id;";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {

                $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                $ban_rank = R_BAN;
                $this->_req->bindParam('ban_rank', $ban_rank, PDO::PARAM_INT);
                if ($this->_req->execute()) {
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function deleteUser($user_id)
    {
        //FIRST UPDATE ALL CONTENT CONCERNED BY THE USER
        $sql = "UPDATE files
        SET files.uploader_id = (SELECT users.id FROM users WHERE users.username =:admin_name)
        WHERE files.uploader_id =:user_id;";
        //NEXT DELETE RELEVANT TABLES
        $sql = $sql .
            "DELETE
        FROM friends
        WHERE friends.user_id = :user_id;

        DELETE
        FROM liked_content
        WHERE liked_content.user_id =:user_id;
        
        DELETE
        FROM bookmarks
        WHERE bookmarks.user_id =:user_id;
        
        DELETE
        FROM comments
        WHERE comments.author_id =:user_id;
        
        DELETE
        FROM users
        WHERE users.id =:user_id
        ";

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('user_id', $user_id, PDO::PARAM_INT);
                $admin_name = ADMIN_NAME;
                $this->_req->bindParam('admin_name', $admin_name, PDO::PARAM_STR);
                if ($this->_req->execute()) {
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function updateUser($user_id, $avatar_path = 'No file')
    {
        $sql = "UPDATE users
        SET username =:username,
        password = COALESCE(:password, password),
        email =:email,
        avatar = COALESCE(:avatar, avatar)
        WHERE id =:id
        ";
        //add update to profile private status after updating db struct

        if (!empty($_POST)) {
            $post = $_POST;
        } else {
            return POST_EMPTY;
        }

        if (!isset($post['Username'], $post['Email'], $post['Password'], $post['PasswordConfirm'])) {
            return POST_EMPTY;
        }
        if ($this->userExist($post['Username']) && $this->getCurrentUser($_COOKIE['auth_token'])['username'] !== $post['Username']) {
            return USERNAME_TAKEN_ERROR;
        }

        if ($post['Password'] !== $post['PasswordConfirm']) {
            return PWD_CONFIRM_ERROR;
        }

        if (strlen($post['Username']) < 3) {
            return USERNAME_LENGTH_ERROR;
        }

        //handle empty string value from front-end
        $pwd_empty = '';
        if ($post['Password'] === '') {
            $pwd_empty = null;
        }


        if (strlen($post['Password']) < 8 && $pwd_empty !== null) {
            return PWD_LENGTH_ERROR;
        }

        if (!filter_var($post['Email'], FILTER_VALIDATE_EMAIL)) {
            return EMAIL_ERROR;
        }

        if ($pwd_empty !== null && $post['Password'] !== $post['PasswordConfirm']) {
            return PWD_CONFIRM_ERROR;
        }

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('id', $user_id, PDO::PARAM_INT);
                $this->_req->bindParam('username', $post['Username'], PDO::PARAM_STR);
                $this->_req->bindParam('email', $post['Email'], PDO::PARAM_STR);
                $this->_req->bindValue('password', password_hash($post['Password'], PASSWORD_BCRYPT), PDO::PARAM_STR);
                if ($avatar_path !== 'No File') {
                    $this->_req->bindParam('avatar', $avatar_path, PDO::PARAM_STR);
                } else {
                    $this->_req->bindValue('avatar', null, PDO::PARAM_NULL);
                }

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
}
