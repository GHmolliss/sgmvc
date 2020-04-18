<?php

namespace sysfiles;

if (!defined('TMVC_VERSION'))
    define('TMVC_VERSION', '1.2.3');

/* directory separator alias */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

/* define myapp directory */
if (!defined('TMVC_MYAPPDIR'))
    define('TMVC_MYAPPDIR', TMVC_BASEDIR . '/myapp');

///* set include_path for spl_autoload */
//set_include_path(get_include_path()
//    . PATH_SEPARATOR . TMVC_MYAPPDIR . 'controllers' . DS
//    . PATH_SEPARATOR . TMVC_MYAPPDIR . 'models' . DS
//    . PATH_SEPARATOR . TMVC_MYAPPDIR . 'configs' . DS
//    . PATH_SEPARATOR . TMVC_MYAPPDIR . 'plugins' . DS
//    . PATH_SEPARATOR . TMVC_MYAPPDIR . 'views' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'myfiles' . DS . 'controllers' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'myfiles' . DS . 'models' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'myfiles' . DS . 'configs' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'myfiles' . DS . 'views' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'sysfiles' . DS . 'controllers' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'sysfiles' . DS . 'models' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'sysfiles' . DS . 'configs' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS
//    . PATH_SEPARATOR . TMVC_BASEDIR . 'sysfiles' . DS . 'views' . DS
//);
//
///* set .php first for speed */
//spl_autoload_extensions('.php,.inc');
//
//$spl_funcs = spl_autoload_functions();
//if ($spl_funcs === false)
//    spl_autoload_register();
//elseif (!in_array('spl_autoload', $spl_funcs))
//    spl_autoload_register('spl_autoload');

/**
 * tmvc
 *
 * main object class
 *
 * @package        TinyMVC
 * @author        Monte Ohrt
 */
class TinyMVC
{
    /* config file values */
    public $config = null;
    /* controller object */
    public $controller = null;
    /* controller method name */
    public $action = null;
    /* server path_info */
    public $path_info = null;
    /* array of url path_info segments */
    public $url_segments = null;

    /**
     * class constructor
     *
     * @access    public
     */
    public function __construct($id = 'default')
    {
        /* set instance */
        self::instance($this, $id);
    }

    /**
     * main method of execution
     *
     * @access    public
     */
    public function main()
    {
        /* set initial timer */
        self::timer('tmvc_app_start');

        /* set path_info */
        $this->path_info = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
            (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
//        $this->path_info = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] :
//            (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');

        /* internal error handling */
        //$this->setupErrorHandling();

        /* include application config */
        include(TMVC_MYAPPDIR . '/configs/config_application.php');
        $this->config = $config;

        /* url remapping/routing */
        $this->setupRouting();
        /* split path_info into array */
        $this->setupSegments();
        /* create controller object */
        $this->setupController();
        /* get controller method */
        $this->setupAction();
        /* run library/script autoloaders */
        $this->setupAutoloaders();

        /* capture output if timing */
//        if ($this->config['timer'])
//            ob_start();

//        echo '<pre>';
//        print_r($this->url_segments);
//        die;
        /* execute controller action */
        $this->controller->{$this->action}();

//        if ($this->config['timer']) {
//            /* insert timing info */
//            $output = ob_get_contents();
//            ob_end_clean();
//            self::timer('tmvc_app_end');
//            echo str_replace('{TMVC_TIMER}', sprintf('%0.5f', self::timer('tmvc_app_start', 'tmvc_app_end')), $output);
//        }
    }

    /**
     * setup error handling for tmvc
     *
     * @access    public
     */
    public function setupErrorHandling()
    {
        if (defined('TMVC_ERROR_HANDLING') && TMVC_ERROR_HANDLING == 1) {
            // catch all uncaught exceptions
            set_exception_handler(array('TinyMVC_ExceptionHandler', 'handleException'));
            require_once('tinymvc_errorhandler.php');
            set_error_handler('TinyMVC_ErrorHandler');
        }
    }

    /**
     * setup url routing for tmvc
     *
     * @access    public
     */
    public function setupRouting()
    {
        if (!empty($this->config['routing']['search']) && !empty($this->config['routing']['replace']))
            echo $this->path_info = preg_replace(
                $this->config['routing']['search'],
                $this->config['routing']['replace'],
                $this->path_info);
    }

    /**
     * setup url segments array
     *
     * @access public
     */
    public function setupSegments()
    {
        $this->url_segments = !empty($this->path_info) ? array_filter(explode('/', $this->path_info)) : null;
    }

    /**
     * setup controller
     *
     * @access    public
     */
    public function setupController()
    {
        /* get controller/method */
        if (!empty($this->config['root_controller'])) {
            $controller_class = $this->config['root_controller'];
            $controller_file = "{$controller_name}.php";
        } else {
            $controller_class = !empty($this->url_segments[1]) ? ucfirst(preg_replace('!\W!', '', $this->url_segments[1]) . $this->config['prefix_controller']) : $this->config['default_controller'];
            $controller_file = "{$controller_class}.php";
            /* if no controller, use default */
            if (!stream_resolve_include_path(TMVC_MYAPPDIR . "/controllers/{$controller_file}")) {
                $controller_class = $this->config['default_controller'];
                $controller_file = "{$controller_class}.php";
            }
        }

        include(TMVC_MYAPPDIR . "/controllers/{$controller_file}");

        /* instantiate the controller */
        $this->controller = new $controller_class(true);
    }

    /**
     * setup controller method (action) to execute
     *
     * @access    public
     */
    public function setupAction()
    {
        if (!empty($this->config['root_action'])) {
            /* user override if set */
            $this->action = $this->config['root_action'];
        } else {
            /* get from url if present, else use default */
            $this->action = !empty($this->url_segments[2]) ? $this->renameAction($this->url_segments[2]) :
                (!empty($this->config['default_action']) ? $this->config['default_action'] : 'actionIndex');

            /* cannot call method names starting with underscore */
            if (substr($this->action, 0, 1) == '_')
                throw new Exception("Action name not allowed '{$this->action}'");
        }
    }

    /**
     * Меняет название `action`
     * index -> actionIndex
     * user-profile -> actionUserProfile
     *
     * @param string $action
     * @return string
     */
    private function renameAction(string $action)
    {
        $actions = explode('-', $action);
        foreach ($actions as &$action) {
            $action = ucfirst($action);
        }

        return $this->config['prefix_action'] . implode(false, $actions);
    }

    /**
     * autoload any libs/scripts
     *
     * @access public
     */
    public function setupAutoloaders()
    {
        include(TMVC_MYAPPDIR . '/configs/config_autoload.php');
        if (!empty($config['libraries'])) {
            foreach ($config['libraries'] as $library)
                if (is_array($library))
                    $this->controller->load->library($library[0], $library[1]);
                else
                    $this->controller->load->library($library);
        }
        if (!empty($config['scripts'])) {
            foreach ($config['scripts'] as $script)
                $this->controller->load->script($script);
        }
        if (!empty($config['models'])) {
            foreach ($config['models'] as $model)
                $this->controller->load->model($model);
        }
    }

    /**
     * instance
     *
     * get/set the tmvc object instance(s)
     *
     * @access    public
     * @param object $new_instance reference to new object instance
     * @param string $id object instance id
     * @return  object $instance reference to object instance
     */
    public static function &instance($new_instance = null, $id = 'default')
    {
        static $instance = array();
        if (isset($new_instance) && is_object($new_instance))
            $instance[$id] = $new_instance;
        return $instance[$id];
    }

    /**
     * timer
     *
     * get/set timer values
     *
     * @access  public
     * @param string $id the timer id to set (or compare with $id2)
     * @param string $id2 the timer id to compare with $id
     * @return  float  difference of two times
     */
    public static function timer($id = null, $id2 = null)
    {
        static $times = array();
        if ($id !== null && $id2 !== null)
            return (isset($times[$id]) && isset($times[$id2])) ? ($times[$id2] - $times[$id]) : false;
        elseif ($id !== null)
            return $times[$id] = microtime(true);
        return false;
    }

}
