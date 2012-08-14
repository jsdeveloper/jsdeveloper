<?php

namespace _References;

class Js
{
    private $dir;

    function __construct($js)
    {
        $this->dir = $js;
    }

    function pack()
    {
        if (is_dir($this->dir)) {
            $files = array();

            $file = glob($this->dir . "*.js");

            foreach ($file as $f) {
                $files[] = str_replace($this->dir, null, $f);
            }
            \Web::minify($this->dir, $files);
            
        } else {
            print ("Error is not a directory or don't exist: " . $this->dir);
        }


    }
}

?>