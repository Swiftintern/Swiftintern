<?php

/**
 * Description of transaction
 *
 * @author Faizan Ayubi
 */
class Transaction extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     */
    protected $_email;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_mihpayid;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_mode;
    
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
     * @type text
     */
    protected $_unmappedstatus;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_txnid;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_amount;
    
    /**
     * @column
     * @readwrite
     * @type datetime
     */
    protected $_addedon;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_productinfo;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_address1;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_zipcode;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_phone;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_field9;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_PG_TYPE;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_bank_ref_num;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_bankcode;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_error;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_error_Message;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_amount_split;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_payuMoneyId;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_discount;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_net_amount_debit;
}
