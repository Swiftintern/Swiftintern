<?php

/**
 * Description of opportunity
 *
 * @author Faizan Ayubi
 */
class Opportunity extends Shared\Model {
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
    protected $_organization_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_title;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_details;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_eligibility;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_category;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_duration;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_location;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type date
     */
    protected $_last_date;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_payment;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_payment_mode;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_application_type;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_type_id;
    
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

    public function save() {
        $this->updated = date('Y-m-d H:i:s');
        parent::save();
    }
}
