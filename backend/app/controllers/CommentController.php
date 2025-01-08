<?php
class CommentController
{
    private $file;
    private $user;
    private $comment;

    public function __construct()
    {
        $this->file = new FileModel();
        $this->user = new UserModel();
        $this->comment = new CommentModel();
    }

    public function getFileComment()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_GET['fileId']) && is_numeric($_GET['fileId'])) {
                    $file_id = $_GET['fileId'];
                    $data = $this->comment->getComment($file_id);
                    if ($data !== null && $data !== REQ_ERROR) {
                        foreach ($data as $comment) {
                            $CommentObjectList[] = new Comment($comment);
                        }
                        foreach ($CommentObjectList as $object) {
                            $array_to_json[] = object_to_array($object);
                        }

                        response($array_to_json, 'Comment List');
                    } else if ($data === null) {
                        response('no_comment');
                    } else {
                        response('req_error');
                    }
                } else {
                    response('no_fileid');
                }
            } else {
                response('forbidden');
            }
        } else {
            response('no_cookie');
        }
    }

    public function sendComment()
    {
        if (isset($_COOKIE['auth_token'])) {
            $auth_token = $_COOKIE['auth_token'];
            if ($this->user->isLoggedIn($auth_token)) {
                if (isset($_POST['comment'], $_GET['fileId']) && is_numeric($_GET['fileId'])) {
                    $user_id = $this->user->getCurrentUserId($auth_token);
                    $file_id = $_GET['fileId'];

                    switch ($this->comment->createComment($user_id, $file_id)) {
                        case COMMENT_EMPTY:
                            response('comment_empty');
                            break;
                        case REQ_ERROR:
                            response('req_error');
                            break;
                        case RETURN_OK:
                            response('comment_ok');
                            break;
                    }
                } else {
                    response('no_post');
                }
            } else {
                response('forbidden');
            }
        } else {
            response('no_cookie');
        }
    }
}
