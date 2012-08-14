<?php

namespace ControllersAPI;

class JsAPI
{
    static function API_GetAll()
    {
        header('Content-type: application/x-javascript');
        
        $js = new \_References\Js("js/");
        
        $js->pack();
    } 

}


?>