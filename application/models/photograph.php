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

    protected function linkedinphoto($logoUrl) {
        $path = APP_PATH . "/public/assets/uploads/images/";
        $this->filename = end(explode("/", $logoUrl));
        $this->type = "";
        $this->size = "";
        if (file_put_contents($path.$this->filename, file_get_contents($logoUrl))) {
            return TRUE;
        }
        return FALSE;
    }

}
