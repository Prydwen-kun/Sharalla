<?php
class UserController
{
    private $user;
    private $file;

    public function __construct()
    {
        $this->user = new UserModel();
        $this->file = new FileModel();
    }

    public function login()
    {
        if (!empty($_POST)) {
            //extract post create var for each key
            if (($Username = $_POST['UserLogin']) && ($Password = $_POST['UserPwd'])) {
                $return = $this->user->login($Username, $Password);
                switch (true) {
                    case is_array($return):
                        $user_token = $return['auth_token'];
                        //send cookie http-only with auth token
                        if (setcookie(
                            'auth_token',
                            $user_token,
                            [
                                'expires' => time() + 86400,
                                'path' => '/',
                                'domain' => DOMAIN_NAME,
                                'secure' => HTTPS_FLAG,
                                'httponly' => true,
                                'samesite' => 'Strict'
                            ]
                        )) {
                            response('connected', 'Connected !');
                        } else {
                            response('cookie_set_error', 'cookie not created !');
                        }
                        break;
                    case $return === CREDENTIALS_ERROR:
                        response('credentials_error', 'Wrong credentials !');
                        break;
                    case $return === REQ_ERROR:
                        response('req_error', 'Request error !');
                        break;
                    case false:
                        response('error', 'Unknown error !');
                        break;
                }
            } else {
                response('post_error', 'Wrong POST param provided !');
            }
        } else {
            response('post_empty', 'No POST provided !');
        }
    }

    public function signup()
    {
        switch ($this->user->signup(R_USER, 'placeholder')) {
            case RETURN_OK:
                require 'app/views/AuthViews/SignupView.php';
                break;
            case PWD_CONFIRM_ERROR:
                response('pwd_confirm_error', 'Enter the same password twice !');
                break;
            case USERNAME_LENGTH_ERROR:
                response('username_length_error', 'Username needs to be over 2 characters !');
                break;
            case PWD_LENGTH_ERROR:
                response('pwd_length_error', 'Password must be over 7 characters long !');
                break;
            case EMAIL_ERROR:
                response('email_error', 'Email provided is not valid !');
                break;
            case USERNAME_TAKEN_ERROR:
                response('username_taken_error', 'Username is already taken !');
                break;
            case REQ_ERROR:
                response('req_error', 'Request error !');
                break;
            case POST_EMPTY:
                response('post_empty', 'No POST provided !');
                break;
            default:
                response('error', 'Unknown error !');
                break;
        }
    }

    public function isConnected()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                response('connected', 'Logged in !');
            } else {
                response('not_connected', 'Disconnected !');
            }
        } else {
            response('no_cookie', 'No cookie provided !');
        }
    }

    public function isUserConnected() #modify this to test friend connection
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
                    $status = $this->user->getUserStatus($_GET['userId']);
                    if ($status !== null) {
                        response($status['status'], 'User status');
                    } else {
                        response('req_error', 'Request error !');
                    }
                } else {
                    response('get_error', 'Wrong get params !');
                }
            } else {
                response('forbidden', 'Sign in to use this ressource !');
            }
        } else {
            response('no_cookie', 'No cookie provided !');
        }
    }

    public function getConnectedUserData()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                $data = $this->user->getCurrentUser($auth_token);
                if ($data !== null) {
                    $connected_user = new User($data);
                    $response_array_to_json = object_to_array($connected_user);
                    response($response_array_to_json, 'User\'s data');
                } else {
                    response('req_error', 'Request error !');
                }
            } else {
                response('forbidden', 'Sign in to use this ressource !');
            }
        } else {
            response('no_cookie', 'Invalid cookie');
        }
    }

    public function getUserData()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
            $user_id = $_GET['userId'];
            if (isset($_COOKIE['auth_token'])) {
                $auth_token = $_COOKIE['auth_token'];
                if ($this->user->isLoggedIn($auth_token)) {
                    $data = $this->user->getUser($user_id);
                    if ($data !== null) {
                        $user = new User($data);
                        $response_array_to_json = object_to_array($user);
                        response($response_array_to_json, 'User data');
                    } else {
                        response('req_error', 'Request error !');
                    }
                } else {
                    response('forbidden', 'Sign in to use this ressource !');
                }
            } else {
                response('no_cookie', 'No cookie');
            }
        } else {
            response('get_error', 'Invalid GET params !');
        }
    }

    public function getUserList()
    {
        $orderTags = ['id', 'username', 'last_login', 'idDESC', 'usernameDESC', 'last_loginDESC'];

        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
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
                    foreach ($UserObjectList as $object) {
                        $array_to_json[] = object_to_array($object);
                    }

                    response($array_to_json, 'Users Data list');
                } else {
                    response('req_error', 'Request error !');
                }
            } else {
                response('forbidden', 'Sign in to use this ressource !');
            }
        } else {
            response('no_cookie', 'Invalid cookie !');
        }
    }

    public function getUsersFromSearch()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['search'])) {
                    //sanitize search
                    $search = htmlspecialchars($_GET['search']);
                    $userList = $this->user->getUsersFromKeyWord($search);

                    if (isset($userList) && $userList !== null) {
                        foreach ($userList as $user) {
                            $UserObjectList[] = new User($user);
                        }
                        foreach ($UserObjectList as $object) {
                            $array_to_json[] = object_to_array($object);
                        }

                        response($array_to_json, 'Users Data list');
                    } else {
                        response('no_result', 'No entry found or DB did not respond !');
                    }
                } else {
                    response('no_search', 'Requested nothing!');
                }
            } else {
                response('forbidden', 'Sign in !');
            }
        } else {
            response('no_cookie', 'Invalid cookie !');
        }
    }

    public function userDelete()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
            $user_id = $_GET['userId'];
            if (isset($_COOKIE['auth_token'])) {
                $auth_token = $_COOKIE['auth_token'];
                if ($this->user->isLoggedIn($auth_token) && ($this->user->getCurrentUserId($auth_token) === $user_id || $this->user->getCurrentUserPower($auth_token) === ADMIN)) {
                    if ($this->user->deleteUser($user_id)) {
                        response('delete_success', 'User deleted');
                    } else {
                        response('delete_error', 'An error occured during deletion !');
                    }
                } else {
                    response('forbidden', 'Sign in to use this ressource !');
                }
            } else {
                response('no_cookie', 'Invalid cookie');
            }
        } else {
            response('get_error', 'Invalid GET params !');
        }
    }

    public function userUpdate()
    {
        if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
            $user_id = $_GET['userId'];
            if (isset($_COOKIE['auth_token'])) {
                $auth_token = $_COOKIE['auth_token'];
                if ($this->user->isLoggedIn($auth_token) && ($this->user->getCurrentUserId($auth_token) === $user_id || $this->user->getCurrentUserPower($auth_token) === ADMIN)) {

                    //recup file path after handling and pass it to update user
                    if (isset($_FILES['Avatar'])) {
                        $filePath = $this->file->createAvatarFile();
                        switch ($filePath) {
                            case FILE_SIZE_ERROR:
                                response('file_size_error', 'File too big for avatar');
                                return;
                            case FILE_EXT_ERROR:
                                response('file_ext_error', 'Wrong extension');
                                return;
                            case FILE_OK:
                                response('file_ok', 'File uploaded');
                                break;
                        }
                    } else {
                        $filePath = 'No File';
                    }

                    switch ($this->user->updateUser($user_id, $filePath)) {
                        case RETURN_OK:
                            response('update_success', 'User updated');
                            break;
                        case REQ_ERROR:
                            response('req_error', 'Request error');
                            break;
                        case PWD_CONFIRM_ERROR:
                            response('pwd_confirm_error', 'Enter the same password twice !');
                            break;
                        case POST_EMPTY:
                            response('post_empty', 'No post provided');
                            break;
                        case USERNAME_LENGTH_ERROR:
                            response('u_name_length', 'Wrong username length !');
                            break;
                        case PWD_LENGTH_ERROR:
                            response('pwd_length', 'Wrong Password length');
                            break;
                        case EMAIL_ERROR:
                            response('email_error', 'Invalid email provided !');
                            break;
                        case USERNAME_TAKEN_ERROR:
                            response('u_name_taken', 'Username is taken !');
                            break;
                        default:
                            response('error', 'Unknown error');
                            break;
                    }
                } else {
                    response('forbidden', 'Sign in to gain access');
                }
            } else {
                response('no_cookie', 'Invalid cookie');
            }
        } else {
            response('get_error', 'Invalid GET params');
        }
    }

    public function logout()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                switch ($this->user->logout($auth_token)) {
                    case true:
                        response('login_out', 'User successfully disconnected !');
                        break;
                    case false:
                        response('status_update_error', 'An error occured when updating user online status !');
                        break;
                    case REQ_ERROR:
                        response('req_error', 'Request error !');
                        break;
                }
            } else {
                response('no_user_connected', 'No user connected !');
            }
        } else {
            response('no_cookie', 'No cookie !');
        }
    }
}
