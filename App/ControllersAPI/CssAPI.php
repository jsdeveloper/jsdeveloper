<?php

namespace ControllersAPI;

class CssAPI
{
    static function API_GetAll()
    {
        header('Content-type: text/css');
        
        $css = new \_References\Css("css/");

        $css->pack();
    }
}


?>