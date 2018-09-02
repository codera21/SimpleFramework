<?php

namespace System\Core;

class Router
{
    public $controller;
    public $method;
    public $args = array();
    protected $AppConfig;

    public function __construct($appConfig)
    {
        $this->AppConfig = $appConfig;
    }

    public function interfaceDefaultRoute()
    {
        $this->controller = $this->AppConfig['InterfaceDefaultRoute']['Controller'];
        $this->method = $this->AppConfig['InterfaceDefaultRoute']['Action'];
    }

    public function adminDefaultRoute()
    {
        $this->controller = $this->AppConfig['AdminDefaultRoute']['Controller'];
        $this->method = $this->AppConfig['AdminDefaultRoute']['Action'];
    }

    public function pathRoute($uri)
    {
        // Remove any trailing slashes and explode
        $parts = trim($uri, '/');
        $parts = explode('/', $parts);
        // The first part of the url is the controller
        $this->controller = array_shift($parts);
        // The second part is the controller method
        if (isset($parts[0])) {
            if (is_numeric($parts[0]))
                $this->method = "Index";
            else
                $this->method = $parts[0];
        } else {
            $this->method = 'Index';
        }
        // Set the args to the rest of the url parts
        $this->args = $parts;
    }

    public function launch()
    {
        $class = $this->controller;
        if (file_exists(APP_PATH . DS . PANEL . DS . "Controllers" . DS . $class . 'Controller.php') && class_exists(PANEL . "\\" . "Controllers\\" . $class . 'Controller')) {
            $controller = PANEL . "\\" . "Controllers\\" . $class . 'Controller';
            $controller = new $controller;
        } // Check if predefined Model class exists and execute through CrudController (this feature is not complete yet)
        elseif (file_exists(APP_PATH . DS . PANEL . DS . "Models" . DS . $class . '.php') && class_exists(PANEL . "\\" . "Models\\" . $class)) {
            $controller = "System\\MVC\\CrudController";
            $controller = new $controller($class, PANEL . "\\" . "Models\\" . $class);
        } else {
            PageNotFound();
        }

        $method = $this->method . "Action";
        // Check if the method exists in the controller
        if (method_exists($controller, $method)) {
            // remove the method in array
            array_shift($this->args);
            call_user_func_array(array($controller, $method), $this->args);
        } else {
            PageNotFound();
        }
    }
}