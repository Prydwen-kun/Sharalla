<?php
class FileController
{
    private $file;
    private $user;

    public function __construct()
    {
        $this->file = new FileModel();
        $this->user = new UserModel();
    }

    public function uploadFile() {}
    public function updateFile() {}
    public function getAllFiles()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                $orderTags = ['id', 'title', 'size', 'idDESC', 'titleDESC', 'sizeDESC'];

                if (isset($_GET['order']) && !isset($_GET['l_size']) && in_array($_GET['order'], $orderTags)) {

                    $fileList = $this->file->getAllFiles($_GET['order']);
                } else if (!isset($_GET['order']) && isset($_GET['l_size']) && is_numeric($_GET['l_size'])) {
                    $fileList = $this->file->getAllFiles('id', $_GET['l_size']);
                } else if (isset($_GET['order']) && isset($_GET['l_size']) && in_array($_GET['order'], $orderTags) && is_numeric($_GET['l_size'])) {
                    $fileList = $this->file->getAllFiles($_GET['order'], $_GET['l_size']);
                } else {
                    $fileList = $this->file->getAllFiles();
                }

                if (isset($fileList) && $fileList !== null) {
                    foreach ($fileList as $file) {
                        $FileObjectList[] = new File($file);
                    }
                    foreach ($FileObjectList as $object) {
                        $array_to_json[] = object_to_array($object);
                    }

                    response($array_to_json, 'Files Data list');
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
    public function getFile() {}
    public function deleteFile() {}
}
