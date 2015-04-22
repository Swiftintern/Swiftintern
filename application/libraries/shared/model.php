<?php

/**
 * Contains similar code of all models and some helpful methods
 *
 * @author Faizan Ayubi
 */

namespace Shared {

    class Model extends \Framework\Model {

        /**
         * @column
         * @readwrite
         * @primary
         * @type autonumber
         */
        protected $_id;

        /**
         * @column
         * @readwrite
         * @type datetime
         */
        protected $_created;

        /**
         * Every time a row is created these fields should be populated with default values.
         */
        public function save() {
            $primary = $this->getPrimaryColumn();
            var_dump($primary);
            $raw = $primary["raw"];
            if (empty($this-> $raw)) {
                $this->setCreated(date("Y-m-d H:i:s"));
            }
            //parent::save();
        }
        
        public function getJsonData() {
            $this->removeProperty($this);
            $var = get_object_vars($this);
            foreach($var as &$value){
                if(is_object($value) && method_exists($value,'getJsonData')){
                    $value = $value->getJsonData();
                }
            }
            return $var;
        }
        
        public function removeProperty() {
            unset($this->_connector);
            unset($this->_table);
            unset($this->_types);
            unset($this->_columns);
            unset($this->_primary);
            unset($this->_validators);
        }

    }

}