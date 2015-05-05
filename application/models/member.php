<?php

/**
 * Description of member
 *
 * @author Faizan Ayubi
 */
class Member extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_organization_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_designation;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 10
     */
    protected $_authority;
    
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