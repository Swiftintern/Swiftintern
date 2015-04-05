<?php

/**
 * Description of question
 *
 * @author Faizan Ayubi
 */
class Question extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_test_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_question;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_type;
}