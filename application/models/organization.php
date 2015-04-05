<?php

/**
 * Description of organization
 *
 * @author Faizan Ayubi
 */
class Organization extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_photo_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_name;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_address;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 25
     */
    protected $_phone;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_country;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_website;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_sector;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_number_employee;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_about;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_fbpage;
    
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
