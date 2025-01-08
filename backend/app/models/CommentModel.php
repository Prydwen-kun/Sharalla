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

    public function getComment(int $file_id)
    {
        $sql = "SELECT comments.id AS id,
                comments.content AS content,
                comments.author_id AS author_id,
                users.username AS author,
                comments.file_id AS file_id,
                comments.post_date AS post_date
                FROM comments
                JOIN users ON users.id = comments.author_id
                WHERE comments.file_id =:file_id
                ORDER BY post_date DESC";
        if (($this->_req = $this->getDb()->prepare($sql)) !== false) {
            $this->_req->bindValue('file_id', $file_id, PDO::PARAM_INT);
            if ($this->_req->execute()) {
                return $this->_req->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } else {
            return REQ_ERROR;
        }
    }

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
