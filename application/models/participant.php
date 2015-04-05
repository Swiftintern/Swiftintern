<?php

/**
 * Description of participant
 *
 * @author Faizan Ayubi
 */
class Participant extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_test_id;

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
     * @length 45
     */
    protected $_score;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_time_taken;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_attempted;
}