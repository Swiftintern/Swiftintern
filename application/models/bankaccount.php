<?php

/**
 * Description of bankaccount
 *
 * @author Faizan Ayubi
 */
class BankAccount extends Shared\Model {
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
    protected $_bank_name;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     */
    protected $_bank_address;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_ifsc_code;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 128
     */
    protected $_account_number;
    
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
