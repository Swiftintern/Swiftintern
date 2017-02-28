<?php

/**
 * Description of organization
 *
 * @author Faizan Ayubi
 */
class Organization extends Shared\Model {

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_photo_id;

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_name;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_country;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_website;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_sector;

    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_type;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 45
     */
    protected $_account = "basic";

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_about;

    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_fbpage;
    
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_linkedin_id;

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
