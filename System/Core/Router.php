<?php

namespace System\Core;

class Router
{
    public $folder;
    public $controllerPath;
    public $classPath;
    public $controller;
    public $method;
    public $args = array();
    protected $AppConfig;

    public function __construct($appConfig)
    {
        $this->AppConfig = $appConfig;
        $this->controllerPath = APP_PATH . DS . PANEL . DS . "Controllers" . DS;
        $this->classPath = PANEL . "\\" . "Controllers\\";
        $this->folder = null;
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
        $parts = trim($uri, '/');
        $parts = explode('/', $parts);
        $firstPart = array_shift($parts);
        // first priority is for the controller
        if (file_exists($this->controllerPath . $firstPart)) {
            $this->folder = $firstPart;
            $this->controller = array_shift($parts);
        } else {
            $this->controller = $firstPart;
        }

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
        if ($this->folder) {
            $file = $this->controllerPath . $this->folder . DS . $class . 'Controller.php';
            $classPath = $this->classPath . $this->folder . '\\' . $class . 'Controller';
        } else {
            $file = $this->controllerPath . $class . 'Controller.php';
            $classPath = $this->classPath . $class . 'Controller';
        }


        if (file_exists($file) && class_exists($classPath)) {
            $controller = new $classPath;

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