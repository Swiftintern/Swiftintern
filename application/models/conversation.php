<?php

/**
 * Description of Conversation
 *
 * @author Faizan Ayubi
 */
class Conversation extends Shared\Model {
    
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
     * @length 256
     */
    protected $_property;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_property_id;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_message_id;
}
