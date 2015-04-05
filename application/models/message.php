<?php

/**
 * Description of message
 *
 * @author Faizan Ayubi
 */
class Message extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_from_user_id;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_to_user_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     */
    protected $_subject;

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_message;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_validity;

    /**
     * fetches a list of replies to a message. It also returns the user_name of the user who replied, 
     * and sorts the replies by their creation date, so that newer messages are displayed first.
     * @return type
     */
    public function getReplies() {
        return self::all(array(
            "message = ?" => $this->getId(),
            "live = ?" => true,
            "deleted = ?" => false
        ), array(
            "*",
            "(SELECT CONCAT(first, \" \", last) FROM user WHERE user.id = message.user)" => "user_name"
        ),
        "created",
        "desc");
    }

    public static function fetchReplies($id) {
        $message = new Message(array(
            "id" => $id
        ));
        return $message->getReplies();
    }

}