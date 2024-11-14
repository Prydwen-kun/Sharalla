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

    public function isUserConnected() #modify this to test friend connection
    {
        if ($this->user->isLoggedIn()) {
            if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
                $status = $this->user->getUserStatus($_GET['userId']);
                if ($status !== null) {
                    echo json_encode([
                        'response' => $status['status'],
                        'message' => 'User status'
                    ]);
                } else {
                    echo json_encode([
                        'response' => 'error',
                        'message' => 'Request error !'
                    ]);
                }
            } else {
                require 'app/views/errorViews/RequestErrorView.php';
            }
        } else {
            require 'app/views/errorViews/ForbiddenErrorView.php';
        }
    }

    public function getConnectedUserData()
    {
        if ($this->user->isLoggedIn()) {
            $data = $this->user->getCurrentUser();
            if ($data !== null) {
                $connected_user = new User($data);
                require 'app/views/UserViews/UserProfileView.php';
            } else {
                require 'app/views/errorViews/RequestErrorView.php';
            }
        } else {
            require 'app/views/errorViews/ForbiddenErrorView.php';
        }
    }

    public function getUserData()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
            $user_id = $_GET['userId'];
            if ($this->user->isLoggedIn()) {
                $data = $this->user->getUser($user_id);
                if ($data !== null) {
                    $user = new User($data);
                    require 'app/views/UserViews/UserView.php';
                } else {
                    require 'app/views/errorViews/RequestErrorView.php';
                }
            } else {
                require 'app/views/errorViews/ForbiddenErrorView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function getUserList()
    {
        $orderTags = ['id', 'username', 'last_login', 'idDESC', 'usernameDESC', 'last_loginDESC'];

        if ($this->user->isLoggedIn()) {
            if (isset($_GET['order']) && !isset($_GET['l_size']) && in_array($_GET['order'], $orderTags)) {

                $userList = $this->user->getAllUser($_GET['order']);
            } else if (!isset($_GET['order']) && isset($_GET['l_size']) && is_numeric($_GET['l_size'])) {
                $userList = $this->user->getAllUser('id', $_GET['l_size']);
            } else if (isset($_GET['order']) && isset($_GET['l_size']) && in_array($_GET['order'], $orderTags) && is_numeric($_GET['l_size'])) {
                $userList = $this->user->getAllUser($_GET['order'], $_GET['l_size']);
            } else {
                $userList = $this->user->getAllUser();
            }

            if (isset($userList) && $userList !== null) {
                foreach ($userList as $user) {
                    $UserObjectList[] = new User($user);
                }

                require 'app/views/UserViews/UserListView.php';
            } else {
                require 'app/views/errorViews/RequestErrorView.php';
            }
        } else {
            require 'app/views/errorViews/ForbiddenErrorView.php';
        }
    }

    public function userDelete()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
            $user_id = $_GET['userId'];
            if ($this->user->isLoggedIn() && ($this->user->getCurrentUserId() == $user_id || $this->user->getCurrentUserPower() == ADMIN)) {
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

    public function userUpdate()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId']) && !empty($_POST)) {
            $user_id = $_GET['userId'];
            if ($this->user->isLoggedIn() && ($this->user->getCurrentUserId() == $user_id || $this->user->getCurrentUserPower() == ADMIN)) {
                if ($this->user->updateUser($user_id)) {
                    require 'app/views/successViews/updateSuccessView.php';
                }
            } else {
                require 'app/views/errorViews/ForbiddenErrorView.php';
            }
        } else {
            require 'app/views/errorViews/RequestErrorView.php';
        }
    }

    public function logout()
    {
        if ($this->user->isLoggedIn()) {
            if ($this->user->logout()) {
                echo json_encode([
                    'response' => 'success',
                    'message' => 'Sign out successfully !'
                ]);
            } else {
                echo json_encode([
                    'response' => 'error',
                    'message' => 'Error login out !'
                ]);
            }
        } else {
            require 'app/views/errorViews/ForbiddenErrorView.php';
        }
    }
}
