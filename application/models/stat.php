<?php

/**
 * Description of view
 *
 * @author Faizan Ayubi
 */
class Stat extends Shared\Model {

    /**
     * @column
     * @readwrite
     * @type text
     * @length 32
     */
    protected $_property;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_property_id;

    /**
     * @column
     * @readwrite
     * @type integer
     */
    protected $_viewed;

    public static function log($property, $property_id) {
        $date = strftime("%Y-%m-%d", strtotime('now'));
        $stat = Stat::first(array(
            "property = ?" => $property,
            "property_id = ?" => $property_id,
            "created LIKE ?" => $date
        ));
        if($stat){
            $stat->viewed += 1;
        } else {
            $stat = new Stat(array(
                "property" => $property,
                "property_id" => $property_id,
                "viewed" => "1"
            ));
        }
        $stat->save();
        return true;
    }

}
