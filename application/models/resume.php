<?php

/**
 * Description of resume
 *
 * @author Faizan Ayubi
 */
class Resume extends Shared\Model {
    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_student_id;

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
     */
    protected $_resume;
    
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