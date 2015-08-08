<?php

/**
 * Description of option
 *
 * @author Faizan Ayubi
 */
class Option extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_ques_id;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_ques_option;
    
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
    protected $_is_answer;
}