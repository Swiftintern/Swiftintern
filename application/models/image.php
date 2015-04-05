<?php

/**
 * Description of image
 *
 * @author Faizan Ayubi
 */
class Image extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_photo_id;

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
    protected $_property;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_property_id;
}