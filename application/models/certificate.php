<?php

/**
 * Description of certificate
 *
 * @author Faizan Ayubi
 */
class Certificate {
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
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
     * @type text
     * @length 255
     */
    protected $_uniqid;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_validity;
}
