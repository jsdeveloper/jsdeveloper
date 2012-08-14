<?php

namespace Controllers;

class ViewController
{
    static function Action_Index($id = null)
    {  
       \F3::set('Title', 'Home');
       \F3::set('MenuHome', 'focus');
    }
    
    static function Action_Search($test = null)
    {
       \F3::set('Title', 'Search');
       \F3::set('MenuSearch', 'focus');
    }
    
    static function Action_Browse()
    {
       \F3::set('Title', 'Browse');
       \F3::set('MenuStock', 'focus');
    }
    
    static function Action_About()
    {
       \F3::set('Title', 'About');
       \F3::set('MenuAbout', 'focus');
    }
    
    static function Action_Contact()
    {
        \F3::set('Title', 'Contact');
        \F3::set('MenuContact', 'focus');
    } 
    
    static function Action_Terms()
    {
        \F3::set('Title', 'Terms');
    }    
}




?>