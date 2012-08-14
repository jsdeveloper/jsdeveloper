<?php

namespace Models;

class Compress
{
    private $String;
    
    public function __construct($str)
    {
        $this->String = $str;
    }
    
    public function Json()
    {
        if(is_array($this->String))
        {
            $compress = null;
            
            $json = json_encode($this->String);
            
            if(strstr($json, '[{'))
            {
                $compress = base64_encode(gzcompress(rawurlencode($json),9));
            }
            else
            {
                $compress = base64_encode(gzcompress(rawurlencode('['.$json.']'),9));
            }
            return $compress;
        }
        else
        {
            return "Is not an Array!";
        }
    }
}

?>