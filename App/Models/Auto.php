<?php

namespace Models;

use \_References\AutoDB as AutoDB;

class Auto
{
    
    public function getAllMake()
    {
        $data = \R::find("make");
            
        return $data;
    }
}

?>