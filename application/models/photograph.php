<?php

/**
 * Description of photograph
 *
 * @author Faizan Ayubi
 */
class Photograph extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     * @length 255
     */
    protected $_filename;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 64
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 64
     */
    protected $_size;
}
