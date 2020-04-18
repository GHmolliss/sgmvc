<?php

namespace sysfiles\plugins;

class TinyMVC_Library_URI
{

    var $path = null;

    function __construct()
    {
        $this->path = TinyMVC::instance()->url_segments;
    }

    function segment($index)
    {
        if (!empty($this->path[$index - 1]))
            return $this->path[$index - 1];
        else
            return false;
    }

    function uri_to_assoc($index)
    {
        $assoc = array();
        for ($x = count($this->path), $y = $index - 1; $y < $x; $y += 2) {
            $assoc_idx = $this->path[$y];
            $assoc[$assoc_idx] = isset($this->path[$y + 1]) ? $this->path[$y + 1] : null;
        }
        return $assoc;
    }

    function uri_to_array($index = 0)
    {
        if (is_array($this->path))
            return array_slice($this->path, $index);
        else
            return false;
    }


}
