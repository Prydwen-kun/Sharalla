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
            if($this->user->isLoggedIn($auth_token)){

            }else{
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
