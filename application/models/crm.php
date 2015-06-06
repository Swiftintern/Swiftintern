<?php

/**
 * Description of crm
 *
 * @author Faizan Ayubi
 */
class CRM extends Shared\Model {

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
     * @type integer
     */
    protected $_first_message_id;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_second_message_id;

}
