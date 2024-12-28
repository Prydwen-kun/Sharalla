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
    public function getConnectedUserFiles()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                $orderTags = ['id', 'title', 'size', 'idDESC', 'titleDESC', 'sizeDESC'];

                $connectedUserId = $this->user->getCurrentUserId($auth_token);

                if (isset($_GET['order']) && !isset($_GET['l_size']) && in_array($_GET['order'], $orderTags)) {

                    $fileList = $this->file->getUserFileList($connectedUserId, $_GET['order']);
                } else if (!isset($_GET['order']) && isset($_GET['l_size']) && is_numeric($_GET['l_size'])) {
                    $fileList = $this->file->getUserFileList($connectedUserId, 'id', $_GET['l_size']);
                } else if (isset($_GET['order']) && isset($_GET['l_size']) && in_array($_GET['order'], $orderTags) && is_numeric($_GET['l_size'])) {
                    $fileList = $this->file->getUserFileList($connectedUserId, $_GET['order'], $_GET['l_size']);
                } else {
                    $fileList = $this->file->getUserFileList($connectedUserId);
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
    public function getEntriesNumber()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                $entries = $this->file->countEntries();
                response($entries, 'Number of entries');
            } else {
                response('forbidden', 'Sign in');
            }
        } else {
            response('no_cookie', 'Invalid cookie !');
        }
    }
    public function getFile() {}
    public function deleteFile()
    {
        //receive file id to delete
        //check user rank and uploader status through token
        //if admin or uploader then authorize deletion
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['fileId']) && is_numeric($_GET['fileId'])) {
                    $file_id = $_GET['fileId'];
                    if ($this->user->getCurrentUserPower($auth_token) == ADMIN || $this->user->getCurrentUserId($auth_token) === $this->file->getUploaderId($file_id)) {
                        if ($this->file->deleteFile($file_id) === RETURN_OK) {
                            response('delete_ok', 'File has been deleted');
                        } else {
                            response('req_error', 'Request error');
                        }
                    }
                } else {
                    response('f_id_error', 'No or wrong file id');
                }
            } else {
                response('forbidden', 'Sign in to use this ressource !');
            }
        } else {
            response('no_cookie', 'Invalid cookie !');
        }
    }
}
