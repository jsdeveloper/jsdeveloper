<?php

namespace _References;


class AutoDB
{
    static function connect()
    {
        \F3::set('DB',new \DB('mysql:host=localhost;port=3306;dbname=auto_dw','root','')); 
    }
    
}


?>