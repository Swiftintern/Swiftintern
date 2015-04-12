<?php

/**
 * The User Model
 *
 * @author Faizan Ayubi
 */
class User extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, alpha, min(3), max(32)
     * @label name
     */
    protected $_name;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @index
     * 
     * @validate required, max(100)
     * @label email address
     */
    protected $_email;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * @index
     * 
     * @validate required, alpha, min(8), max(32)
     * @label password
     */
    protected $_password;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate alpha, min(8), max(15)
     * @label name
     */
    protected $_phone;
    
    /**
     * @column
     * @readwrite
     * @type text
     * 
     * @label access_token
     */
    protected $_access_token;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_login_number;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 100
     * 
     * @validate required, alpha, min(3), max(32)
     * @label name
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_validity;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_last_ip;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_last_login;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;   

    public function isFriend($id) {
        $friend = Friend::first(array(
                    "user" => $this->getId(),
                    "friend" => $id
        ));

        if ($friend) {
            return true;
        }
        return false;
    }

    public static function hasFriend($id, $friend) {
        $user = new self(array(
            "id" => $id
        ));
        return $user->isFriend($friend);
    }

    /**
     * Retursn the latest File row
     * @return type
     */
    public function getFile() {
        return File::first(array(
            "user = ?" => $this->id,
            "live = ?" => true,
            "deleted = ?" => false
        ), array("*"), "id", "DESC");
    }

}
