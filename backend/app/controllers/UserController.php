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
            if ($this->user->signup(R_USER, 'placeholder')) {
                require 'app/views/AuthViews/SignupView.php';
            } else {
                require 'app/views/errorViews/SignupErrorView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function isUserConnected()
    {
        if ($this->user->isLoggedIn()) {
            echo json_encode([
                'response' => 'connected',
                'message' => 'User is connected !'
            ]);
        } else {
            echo json_encode([
                'response' => 'not_connected',
                'message' => 'User isn\'t connected !'
            ]);
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

    public function getUserList()
    {

        $userList = [];
        if (isset($_GET['order']) && !isset($_GET['l_size'])) {

            $userList = $this->user->getAllUser($_GET['order']);
        } else if (!isset($_GET['order']) && isset($_GET['l_size'])) {
            $userList = $this->user->getAllUser('id', $_GET['l_size']);
        } else if (isset($_GET['order']) && isset($_GET['l_size'])) {
            $userList = $this->user->getAllUser($_GET['order'], $_GET['l_size']);
        } else {
            $userList = $this->user->getAllUser();
        }

        if (!empty($userList)) {
            foreach ($userList as $user) {
                $UserObjectList[] = new User($user);
            }

            require 'app/views/UserViews/UserListView.php';
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function userDelete($user_id)
    {
        if (is_numeric($user_id)) {
            if ($this->user->isLoggedIn() && $this->user->getCurrentUserPower() == ADMIN) {
                if ($this->user->deleteUser($user_id)) {
                    require 'app/views/successViews/deleteSuccessView.php';
                } else {
                    require 'app/views/errorViews/userDeleteErrorView.php';
                }
            } else {
                require 'app/views/errorViews/ForbiddenErrorView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }
}
