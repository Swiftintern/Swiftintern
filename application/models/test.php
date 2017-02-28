<?php

/**
 * Description of test
 *
 * @author Faizan Ayubi
 */
class Test extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_type;

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
    protected $_syllabus;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_subject;
    
    /**
     * @column
     * @readwrite
     * @type text
     */
    protected $_level;
    
    /**
     * @column
     * @readwrite
     * @type time
     */
    protected $_time_limit;
    
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
