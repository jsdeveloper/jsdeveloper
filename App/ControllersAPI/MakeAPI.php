<?php

namespace ControllersAPI;

use \Models\Compress as Compress;
use \Models\Auto as Auto;


class MakeAPI
{
    private $Auto;

    
    static function API_List()
    {
      //  $Make = new \Axon('Make');
         
         
         $Auto = new Auto();
         
         $MakeList = $Auto->getAllMake();
         
         $jsonCompress = new Compress($MakeList);
         
  //       print($jsonCompress->Json());
        
      // $test = \R::count("Make");
        
        print_r($MakeList);

       // $jsonCompress = new Compress($Make);

      //  print ($jsonCompress->Json());
    }
    
    static function API_Insert($make)
    {
        if(isset($_GET['key']))
        {
            $key = $_GET['key'];
            
            if($key == "")
            {
                
            }
        }
        if(!is_null($make))
        {
          $tmake = new Auto();
          $tmake->insert_make($make);
          
          echo "Done: ".$make;
        }
    }
}

?>