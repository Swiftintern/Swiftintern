<?php

/**
 * Description of newsletter
 *
 * @author Faizan Ayubi
 */
class Newsletter extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     * @length 128
     */
    protected $_subject;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_message;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_user_group;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_sent_count;
    
    /**
     * @column
     * @readwrite
     * @type date
     */
    protected $_scheduled;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}