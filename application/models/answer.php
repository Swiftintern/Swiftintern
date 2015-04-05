<?php

/**
 * Description of answer
 *
 * @author Faizan Ayubi
 */
class Answer extends Shared\Model {
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
    protected $_ques_id;
    
    /**
     * @column
     * @readwrite
     * @type text
     * @length 256
     *
     * @validate required
     * @label answer
     */
    protected $_answer;
    
}
