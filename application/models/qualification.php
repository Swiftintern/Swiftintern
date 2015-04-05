<?php

/**
 * Description of qualification
 *
 * @author Faizan Ayubi
 */
class Qualification extends Shared\Model {
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
     */
    protected $_degree;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_major;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_gpa;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_passing_year;
}
