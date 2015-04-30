<?php

/**
 * A Model class for linkedin meta
 *
 * @author Faizan Ayubi
 */
class Meta extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_property;

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_property_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_meta_key;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_meta_value;
    
}
