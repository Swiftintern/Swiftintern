<?php

/**
 * Description of blogpost
 *
 * @author Faizan Ayubi
 */
class BlogPost {
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
     * @length 128
     */
    protected $_title;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_content;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_category;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_validity;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}
