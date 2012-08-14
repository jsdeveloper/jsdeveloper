<?php

namespace ControllersAPI;

use \Models\Compress as Compress; 

class ModelAPI
{
    static function API_List($id = null)
    {        
        if($id != null && is_numeric($id))
        {
            $Model = new \Axon('Model');
            
            $ModelList = $Model->afind('MakeId='.$id);
            
            $jsonCompress = new Compress($ModelList);

            print($jsonCompress->Json());
        }
        else
        {
            \F3::error(404);
        }
    }
}

?>