<?php

/**
 * Description of view
 *
 * @author Faizan Ayubi
 */
class View extends Shared\Model {
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
     * @type datetime
     */
    protected $_date;
    
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_viewed;
}