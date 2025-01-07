<?php
class CommentModel extends CoreModel
{
    private $_req;

    public function __destruct()
    {
        if (!empty($this->_req)) {
            $this->_req->closeCursor();
        }
    }

    public function getComment(int $file_id) {}

    public function createComment(int $user_id, int $file_id)
    {

        if (strlen($_POST['comment']) <= 0) {
            $comment = $_POST['comment'];
        } else {
            return COMMENT_EMPTY;
        }

        $sql = "INSERT INTO comments(id,content,author_id,file_id,post_date)
         VALUES(DEFAULT,:comment,:author_id,:file_id,DEFAULT)";
        if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
            $this->_req->bindValue("comment", $comment, PDO::PARAM_STR);
            $this->_req->bindValue("author_id", $user_id, PDO::PARAM_INT);
            $this->_req->bindValue("file_id", $file_id, PDO::PARAM_INT);
            if ($this->_req->execute()) {
                return RETURN_OK;
            } else {
                return REQ_ERROR;
            }
        } else {
            return REQ_ERROR;
        }
    }
}
