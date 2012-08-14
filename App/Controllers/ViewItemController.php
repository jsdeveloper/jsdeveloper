<?php

namespace Controllers;

class ViewItemController
{
    static function Action_Index()
    {  
      \F3::set('Title', 'Home');
      \F3::set('MenuHome', 'focus');
     // \F3::set('HideDefaultLayout', true);
     // \F3::set('CustomLayout', 'layout');
      
      if(isset($_GET['item']))
      {
         echo $_GET['item'];
      }
      else
      {
        \F3::error(404);
      }
      
      // 
       
       
    }
    
    static function Action_Search($test = null)
    {
      \F3::set('HideLayout', true);
      
      if(isset($_GET['item']))
      {
         echo $_GET['item']."<br>";
      }
      echo "just Search";
      
    }
    
    static function Action_Browse()
    {
    //   \F3::set('Title', 'Browse');
     //  \F3::set('MenuStock', 'focus');
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