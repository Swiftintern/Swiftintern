<?php

/**
 * Description of follower
 *
 * @author Faizan Ayubi
 */
class Follower {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_following_id;
    
}
