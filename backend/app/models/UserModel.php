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

        if (strlen($post['Password']) <= 8) {
            return false;
        }

        if (!filter_var($post['Email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $query = "INSERT INTO users VALUES(DEFAULT,:email,:username,:password,DEFAULT,:rank,:avatar)";

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
            return true;
        }
        return false;
    }

    public function lastLoginUpdate()
    {
        $query = "UPDATE users
        SET last_login = DEFAULT
        WHERE users.id = :user_id";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->execute(['user_id' => $_SESSION[APP_TAG]['user_id']]);
    }

    public function logout()
    {
        session_destroy();
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
        if ($this->isLoggedIn()) {
            $query =
                "SELECT users.id AS id,
            username,
            password,
            email,
            last_login,
            users.rank AS rank_id,
            ranks.label AS rank,
            ranks.power AS power,
            users.avatar AS avatar
             FROM users
             LEFT JOIN ranks ON users.rank = ranks.id 
             WHERE users.id = :user_id";

            $this->_req = $this->getDb()->prepare($query);
            $this->_req->execute(['user_id' => $_SESSION[APP_TAG]['user_id']]);
            return $this->_req->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }

    public function updateUser() {}

    public function getUser($user_id)
    {
        $query =
            "SELECT users.id AS id,
                username,
                password,
                email,
                last_login,
                users.rank AS rank_id,
                ranks.label AS rank,
                ranks.power AS power,
                users.avatar AS avatar
            FROM users
            LEFT JOIN ranks ON users.rank = ranks.id
            WHERE users.id = :user_id";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->execute(['user_id' => $user_id]);
        return $this->_req->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUser($orderBy, $limit) {}

    public function deleteUser() {}
}
