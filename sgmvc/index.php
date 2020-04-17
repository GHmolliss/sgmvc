<?php

use app\vendor\sgmvc\SGMVC;

define('SG_ROOT', __DIR__);
//define('SG_DS', DIRECTORY_SEPARATOR);
//define('SG_PS', PATH_SEPARATOR);

require_once SG_ROOT . '/app/vendor/sgmvc/config/config.php';

(new SGMVC);