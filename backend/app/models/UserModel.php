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

    public function signup(int $rank, string $avatar): bool
    {
        if (!empty($_POST)) {
            $post = $_POST;
        } else {
            return false;
        }

        if ($post['Password'] !== $post['PasswordConfirm']) {
            return false;
        }

        if (strlen($post['Username']) < 3) {
            return false;
        }

        if (strlen($post['Password']) < 8) {
            return false;
        }

        if (!filter_var($post['Email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $query = "INSERT INTO users VALUES(DEFAULT,:email,:username,:password,DEFAULT,:rank,:avatar,DEFAULT)";

        if (!$this->userExist($post['Username'])) {
            $this->_req = $this->getDb()->prepare($query);
            $this->_req->bindValue("username", $post['Username'], PDO::PARAM_STR);
            $this->_req->bindValue("email", $post['Email'], PDO::PARAM_STR);
            $this->_req->bindValue("password", password_hash($post['Password'], PASSWORD_BCRYPT), PDO::PARAM_STR);
            $this->_req->bindValue("rank", $rank, PDO::PARAM_INT);
            $this->_req->bindValue("avatar", $avatar, PDO::PARAM_INT);
            return $this->_req->execute();
        } else {
            return false;
        }
    }

    public function login($username, $password)
    {

        $query = "SELECT users.id AS id,
        users.password AS password,
        users.email AS email,
        last_login,
        ranks.label AS rank
        ranks.power AS power 
        FROM users 
        JOIN rank ON users.rank = rank.id 
        WHERE username = :username";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->execute(['username' => $username]);
        $this->_user = $this->_req->fetch(PDO::FETCH_ASSOC);
        $this->_req->closeCursor();

        if ($this->_user && password_verify($password, $this->_user['password'])) {
            $_SESSION[APP_TAG]['user_id'] = $this->_user['id'];
            $_SESSION[APP_TAG]['rank'] = $this->_user['rank'];
            $_SESSION[APP_TAG]['user_power'] = $this->_user['power'];

            if ($this->lastLoginUpdate()) {
                return $this->statusUpdate(STATUS_ONLINE);
            } else {
                session_destroy();
                return false;
            }
        }
        return false;
    }

    public function statusUpdate($status_id)
    {
        $query = "UPDATE status
        SET label_id = :status_id
        WHERE status.user_id =:user_id";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->bindParam('user_id', $_SESSION[APP_TAG]['user_id'], PDO::PARAM_INT);
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
    public function lastLoginUpdate()
    {
        $query = "UPDATE users
        SET last_login = DEFAULT
        WHERE users.id = :user_id";
        $this->_req = $this->getDb()->prepare($query);
        return $this->_req->execute(['user_id' => $_SESSION[APP_TAG]['user_id']]);
    }

    public function logout()
    {
        if ($this->statusUpdate(STATUS_OFFLINE)) {
            session_destroy();
            return true;
        } else {
            session_destroy();
            return false;
        }
    }

    public function isLoggedIn()
    {
        return isset($_SESSION[APP_TAG]['user_id']);
    }

    public function getCurrentUserId()
    {
        return $_SESSION[APP_TAG]['user_id'];
    }

    public function getCurrentUserPower()
    {
        return $_SESSION[APP_TAG]['user_power'];
    }

    public function getCurrentUser()
    {
        $query =
            "SELECT users.id AS id,
            username,
            email,
            last_login,
            users.rank AS rank_id,
            ranks.label AS rank,
            ranks.power AS power,
            users.avatar AS avatar,
            users.signup_date
             FROM users
             LEFT JOIN ranks ON users.rank = ranks.id 
             WHERE users.id = :user_id";

        $this->_req = $this->getDb()->prepare($query);
        if ($this->_req->execute(['user_id' => $_SESSION[APP_TAG]['user_id']])) {
            return $this->_req->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function getUser($user_id)
    {
        //if profile private only admin should be able to access
        $query =
            "SELECT users.id AS id,
                username,
                email,
                last_login,
                users.rank AS rank_id,
                ranks.label AS rank,
                ranks.power AS power,
                users.avatar AS avatar,
                users.signup_date
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

    public function getAllUser($orderBy = 'id', $limit = 10)
    {
        switch ($orderBy) {
            case 'id':
                $sql = "SELECT users.id AS id,
                username,
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY id
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY username
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY last_login
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY id DESC
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY username DESC
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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
                email,
                last_login,
                ranks.label AS rank,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            ORDER BY last_login DESC
            LIMIT :list_limit";

                try {
                    if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                        $this->_req->bindParam('list_limit', $limit, PDO::PARAM_INT);
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

    public function updateUser($user_id)
    {
        $post = $_POST;
        $sql = "UPDATE users
        SET username =:username,
        password = COALESCE(:password, password),
        email =:email,
        WHERE id =:id
        ";
        //add update to profile private status after updating db struct
        if (!(isset($post['Password']) && isset($post['PasswordConfirm']))) {
            return false;
        }

        if ($post['Password'] !== null && $post['Password'] !== $post['PasswordConfirm']) {
            return false;
        }

        try {
            if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
                $this->_req->bindParam('id', $user_id, PDO::PARAM_INT);
                $this->_req->bindParam('username', $post['Username'], PDO::PARAM_STR);
                $this->_req->bindParam('email', $post['Email'], PDO::PARAM_STR);
                $this->_req->bindParam('password', $post['Password'], PDO::PARAM_STR);
                $this->_req->bindParam('avatar', $post['Avatar'], PDO::PARAM_STR);

                if ($this->_req->execute()) {
                    return true;
                }
                return false;
            }
            return false;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}
