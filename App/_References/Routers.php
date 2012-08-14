<?php

namespace _References;

class Routers
{
    static function Load()
    {
        
        \F3::route('GET /@Controller/@Action/@Params*', '_References\Controllers::Load');
        \F3::route('GET /@Controller/@Action', '_References\Controllers::Load');
        \F3::route('GET /@Controller', '_References\Controllers::Load');
        \F3::route('GET /*', '_References\Controllers::Load');
        \F3::route('POST /@Controller/@Action/@Params*', '_References\Controllers::Load');
        \F3::route('POST /@Controller/@Action', '_References\Controllers::Load');
        \F3::route('POST /@Controller', '_References\Controllers::Load');
        \F3::route('POST /', '_References\Controllers::Load');
    }
    
    static function API()
    {
        \F3::route('GET /API/@ControllerAPI/@ActionAPI/@ParamsAPI*', '_References\Controllers::LoadAPI');
        \F3::route('GET /API/@ControllerAPI/@ActionAPI', '_References\Controllers::LoadAPI');
        \F3::route('GET /API/@ControllerAPI', '_References\Controllers::LoadAPI');
        \F3::route('GET /API/', '_References\Controllers::LoadAPI');
        \F3::route('POST /API/@ControllerAPI/@ActionAPI/@ParamsAPI*', '_References\Controllers::LoadAPI');
        \F3::route('POST /API/@ControllerAPI/@ActionAPI', '_References\Controllers::LoadAPI');
        \F3::route('POST /API/@ControllerAPI', '_References\Controllers::LoadAPI');
        \F3::route('POST /API/', '_References\Controllers::LoadAPI');
    }
}

?>