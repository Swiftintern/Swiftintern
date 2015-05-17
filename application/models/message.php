<?php

/**
 * Description of message
 *
 * @author Faizan Ayubi
 */
class Message extends Shared\Model {
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     */
    protected $_subject;

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_body;
}