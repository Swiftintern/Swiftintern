<?php

/**
 * Description of sponsored
 *
 * @author Faizan Ayubi
 */
class Sponsored extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_opportunity_id;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_user_id;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_start;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_end;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_is_active;
    
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
