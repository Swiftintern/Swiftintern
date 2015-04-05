<?php

/**
 * Description of order
 *
 * @author Faizan Ayubi
 */
class Order extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_transaction_id;
    
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
     * @length 32
     */
    protected $_status;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}