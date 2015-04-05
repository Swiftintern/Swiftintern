<?php

/**
 * Description of work
 *
 * @author Faizan Ayubi
 */
class Work extends Shared\Model {
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
    protected $_organization_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 64
     */
    protected $_duration;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 64
     */
    protected $_designation;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_responsibility;
}
