<?php
class UserModel extends CoreModel
{
    private $_req;

    public function __destruct()
    {
        if (!empty($this->_req)) {
            $this->_req->closeCursor();
        }
    }

    public function userExist() {}

    public function signup() {}

    public function login() {}

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

    public function getCurrentUserPower(){
        return $_SESSION['user_power'];
    }

    public function getCurrentUser(){

    }

    public function updateUser(){

    }

    public function getUser(){

    }

    public function getAllUser($orderBy,$limit){

    }

    public function deleteUser(){
        
    }
}
