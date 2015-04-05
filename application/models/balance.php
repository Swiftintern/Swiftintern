<?php

/**
 * Description of balances
 *
 * @author Faizan Ayubi
 */
class Balance {
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
     * @length 32
     */
    protected $_amount;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_updated;
}
