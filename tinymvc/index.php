<?php

use sysfiles\TinyMVC;

/* PHP error reporting level, if different from default */
error_reporting(E_ALL);

/* if the /tinymvc/ dir is not up one directory, uncomment and set here */
//define('TMVC_BASEDIR','../tinymvc/');

/* if the /myapp/ dir is not inside the /tinymvc/ dir, uncomment and set here */
//define('TMVC_MYAPPDIR','/path/to/myapp/');

/* define to 0 if you want errors/exceptions handled externally */
define('TMVC_ERROR_HANDLING', 1);

/* directory separator alias */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

/* set the base directory */
if (!defined('TMVC_BASEDIR'))
    define('TMVC_BASEDIR', __DIR__);

spl_autoload_register(function ($class_name) {
    include TMVC_BASEDIR . "/{$class_name}.php";
});

//echo TMVC_BASEDIR . "\n";
/* include main tmvc class */
//require(TMVC_BASEDIR . 'sysfiles' . DS . 'TinyMVC.php');

/* instantiate */
$tmvc = new TinyMVC();

/* tally-ho! */
$tmvc->main();
