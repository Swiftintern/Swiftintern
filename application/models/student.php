<?php

/**
 * Description of student
 *
 * @author Faizan Ayubi
 */
class Student extends Shared\Model {
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
     */
    protected $_about;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_city;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_skills;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}