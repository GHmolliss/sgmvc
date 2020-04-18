<?php

use sysfiles\plugins\TinyMVC_Controller;

class IndexController extends TinyMVC_Controller
{
    function actionIndex()
    {
        $this->view->display('index_view');
    }
}
