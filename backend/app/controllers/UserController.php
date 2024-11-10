<?php
class UserController
{
    private $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function login()
    {
        if (!empty($_POST)) {
            //extract post create var for each key
            $Username = $_POST['Username'];
            $Password = $_POST['Password'];

            if ($this->user->login($Username, $Password)) {
                require 'app/views/LoginView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function signup()
    {
        if (!empty($_POST)) {
            if ($this->user->signup(3, 'placeholder')) {
                require 'app/views/AuthViews/SignupView.php';
            } else {
                require 'app/views/errorViews/SignupErrorView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function getConnectedUserData()
    {
        $connected_user = $this->user->getCurrentUser();
        if ($connected_user !== null) {
            require 'app/views/UserViews/UserProfileView.php';
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function getUserData($user_id)
    {
        $user = $this->user->getUser($user_id);
        if ($user !== null) {
            require 'app/views/UserViews/UserView.php';
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }
}
