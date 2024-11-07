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
                echo json_encode(['response' => 'connected']);
            }
        }
    }

    public function signup()
    {
        if (!empty($_POST)) {
            if ($this->user->signup(3, 'placeholder')) {
                echo json_encode(['response' => 'user_created']);
            } else {
                echo json_encode(['response' => 'error']);
            }
        }
    }
}
