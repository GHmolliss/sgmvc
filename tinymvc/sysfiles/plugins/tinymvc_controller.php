<?php

namespace sysfiles\plugins;

use sysfiles\TinyMVC;
use sysfiles\plugins\TinyMVC_Load;
use sysfiles\plugins\TinyMVC_View;

/**
 * TinyMVC_Controller
 *
 * @package        TinyMVC
 * @author        Monte Ohrt
 */
class TinyMVC_Controller
{
    /**
     * class constructor
     *
     * @access public
     */
    function __construct()
    {
        /* save controller instance */
        TinyMVC::instance($this, 'controller');

        /* instantiate load library */
        $this->load = new TinyMVC_Load;

        /* instantiate view library */
        $this->view = new TinyMVC_View;
    }

    /**
     * index
     *
     * the default controller method
     *
     * @access    public
     */
    function index()
    {
    }

    /**
     * __call
     *
     * gets called when an unspecified method is used
     *
     * @access    public
     */
    function __call($function, $args)
    {

        throw new Exception("Unknown controller method '{$function}'");

    }

}
