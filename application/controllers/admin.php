<?php

/**
 * The admin controller which has hihest privilege
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;
use Framework\RequestMethods as RequestMethods;

class Admin extends Controller {

    public function index() {
        $this->willRenderLayoutView = false;
        $this->willRenderActionView = false;
            
        $response = $this->_checkitem(array(
            "model" => "user",
            "where" => array(
                "id = ? " => "31"
            )
        ));
        
        var_dump($response);
    }
    
    public function loginuser($param) {
        
    }

}
