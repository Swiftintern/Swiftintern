<?php

/**
 * Description of crm
 *
 * @author Faizan Ayubi
 */
class CRM extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     */
    protected $_email;
    
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
     * @type integer
     */
    protected $_validity;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}
