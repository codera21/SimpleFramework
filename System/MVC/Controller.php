<?php

namespace System\MVC;

use System\Core\Loader;

abstract class Controller
{
    public $load;
    private static $instance;

    public function __construct()
    {
        self::$instance =& $this;
        $this->load = new Loader();

        if (!ob_get_status()) {
            if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
                ob_start('ob_gzhandler');
            else
                ob_start();
        }
        // calling it
        $this->example();
    }

    // if you want function to be executed all over the app
    private function example()
    {
        // example code
    }
}