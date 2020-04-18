<?php

namespace sysfiles\plugins;

/**
 * TinyMVC_Model
 *
 * @package        TinyMVC
 * @author        Monte Ohrt
 */
class TinyMVC_Model
{
    /**
     * $db
     *
     * the database object instance
     *
     * @access    public
     */
    var $db = null;

    /**
     * class constructor
     *
     * @access    public
     */
    function __construct($poolname = null)
    {
        $this->db = TinyMVC::instance()->controller->load->database($poolname);
    }

}
