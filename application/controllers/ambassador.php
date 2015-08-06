<?php

/**
 * Description of ambassador
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class Ambassador extends Admin {
    
    /**
     * Stats for ambassador
     * 
     * @before _secure, changeLayout
     */
    public function index() {
        $this->seo(array("title" => "Campus Ambassadors", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        
    }
    
    public function register() {
        
    }
}
