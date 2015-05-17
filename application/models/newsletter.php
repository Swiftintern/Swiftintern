<?php

/**
 * Description of newsletter
 *
 * @author Faizan Ayubi
 */
class Newsletter extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_message_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_user_group;
    
    /**
     * @column
     * @readwrite
     * @type date
     */
    protected $_scheduled;
}