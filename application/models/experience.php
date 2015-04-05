<?php

/**
 * Description of experience
 *
 * @author Faizan Ayubi
 */
class Experience {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_organization_id;

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
    protected $_title;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_details;
    
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
    protected $_validity;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}
