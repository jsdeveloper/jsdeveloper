<?php

namespace _References;

class Css
{
    private $dir;

    function __construct($css)
    {
        $this->dir = $css;
    }

    function pack()
    {
        if (is_dir($this->dir)) {
            $files = array();

            $file = glob($this->dir . "*.css");

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