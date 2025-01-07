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

    public function uploadFile()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_FILES['Upload']) && !empty($_FILES['Upload']['name'])) {
                    $user_id = $this->user->getCurrentUserId($auth_token);
                    if (isset($_POST['Title'])) {
                        $title = $_POST['Title'];

                        switch ($this->file->createFile($user_id, $title)) {
                            case FILE_UPLOAD_ERROR:
                                response('file_upload_error');
                                break;
                            case FILE_SIZE_ERROR:
                                response('file_size_error');
                                break;
                            case FILE_EXT_ERROR:
                                response('file_ext_error');
                                break;
                            case FILE_TITLE_SIZE_ERR:
                                response('file_title_size_err');
                                break;
                            case FILE_DESC_SIZE_ERR:
                                response('file_desc_size_err');
                                break;
                            case REQ_ERROR:
                                response('req_error');
                                break;
                            case RETURN_OK:
                                response('upload_ok');
                                break;
                            default:
                                response('error');
                                break;
                        }
                    } else {
                        switch ($this->file->createFile($user_id)) {
                            case FILE_UPLOAD_ERROR:
                                response('file_upload_error');
                                break;
                            case FILE_SIZE_ERROR:
                                response('file_size_error');
                                break;
                            case FILE_EXT_ERROR:
                                response('file_ext_error');
                                break;
                            case REQ_ERROR:
                                response('req_error');
                                break;
                            case RETURN_OK:
                                response('upload_ok');
                                break;
                            default:
                                response('error');
                                break;
                        }
                    }
                } else {
                    response('no_upload');
                }
            } else {
                response('forbidden', 'Sign in to use this ressource !');
            }
        } else {
            response('no_cookie', 'Invalid cookie !');
        }
    }
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
                    //array count available under param0
                    $results_count = $this->file->getResultsCount();
                    if ($results_count !== false) {
                        response($array_to_json, 'Files Data list', $results_count);
                    } else {
                        response($array_to_json, 'Files Data list', 'count_error');
                    }
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

                    //array count available under param0
                    $results_count = $this->file->getUserFileCount($connectedUserId);
                    if ($results_count !== false) {
                        response($array_to_json, 'Files Data list', $results_count);
                    } else {
                        response($array_to_json, 'Files Data list', 'count_error');
                    }
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
    public function getFile()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['fileId']) && is_numeric($_GET['fileId'])) {
                    $file_id = $_GET['fileId'];
                    $data = $this->file->readFile($file_id);

                    switch ($data) {
                        case REQ_PREP_ERROR:
                            response('req_prep_error', 'req_prep_error');
                            break;
                        case REQ_ERROR:
                            response('req_error', 'Request error');
                            break;
                        case null:
                            response('no_result', 'No result found');
                            break;
                        case false:
                            response('fetch_error', 'Db fetch error');
                            break;
                        default:
                            $fileObject = new File($data);
                            $file = object_to_array($fileObject);
                            response($file, 'File');
                            break;
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

    public function downloadFile()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['fileId']) && is_numeric($_GET['fileId'])) {
                    if ($this->file->downloadFile() !== false) {
                        
                    } else {
                        response('no_file');
                    }
                } else {
                    response('no_req');
                }
            } else {
                response('forbidden');
            }
        } else {
            response('no_cookie');
        }
    }
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
