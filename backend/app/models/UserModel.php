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

    public function userExist() {}

    public function signup() {}

    public function login($username, $password)
    {

        $query = "SELECT users.id AS id,
        users.password AS password,
        users.email AS email,
        last_login,
        role.label AS role 
        FROM users 
        JOIN role ON role_id_fk = role.id 
        WHERE username = :username";
        $this->_req = $this->getDb()->prepare($query);
        $this->_req->execute(['username' => $username]);
        $this->_user = $this->_req->fetch(PDO::FETCH_ASSOC);
        $this->_req->closeCursor();

        if ($this->_user && password_verify($password, $this->_user['password'])) {
            $_SESSION['user_id'] = $this->_user['id'];
            $_SESSION['role'] = $this->_user['role'];
            return true;
        }
        return false;
    }

    public function lastLoginUpdate() {}

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUserId()
    {
        return $_SESSION['user_id'];
    }

    public function getCurrentUserPower()
    {
        return $_SESSION['user_power'];
    }

    public function getCurrentUser() {}

    public function updateUser() {}

    public function getUser() {}

    public function getAllUser($orderBy, $limit) {}

    public function deleteUser() {}
}
