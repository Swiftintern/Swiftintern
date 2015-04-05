<?php

/**
 * Description of application
 *
 * @author Faizan Ayubi
 */
class Application extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_student_id;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_opportunity_id;
    
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
     * @length 32
     */
    protected $_status;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}
