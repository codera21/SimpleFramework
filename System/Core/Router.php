<?php
namespace System\Core;

class Router
{

    public $controller;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $args = array();

    protected $SystemConfig;

    public function __construct($systemConfig)
    {

        $this->SystemConfig = $systemConfig;
    }

    /**
     * Sets the default controller and method defined in the Config
     *
     * @return void
     */
    public function interfaceDefaultRoute()
    {
        $this->controller = $this->SystemConfig['InterfaceDefaultRoute']['Controller'];
        $this->method = $this->SystemConfig['InterfaceDefaultRoute']['Action'];
    }

    public function adminDefaultRoute()
    {
        $this->controller = $this->SystemConfig['AdminDefaultRoute']['Controller'];
        $this->method = $this->SystemConfig['AdminDefaultRoute']['Action'];
    }

    /**
     * Sets the controller and method depending on the path
     *
     * @return void
     */
    public function pathRoute($uri = '')
    {
        // Remove any trailing slashes
        $parts = trim($uri, '/');

        // Explode the url
        $parts = explode('/', $parts);

        // The first part of the url is the controller
        $this->controller = array_shift($parts);

        // The second part is the controller method
        // we check if it's set

        if (isset($parts[0])) {
            // Set the method to the second part

            if (is_numeric($parts[0]))
                $this->method = "Index";
            else
                $this->method = $parts[0];
        } else
            $this->method = 'Index';// WebInterface method (index) called if no method specified


        // Set the args to the rest of the url parts
        $this->args = $parts;
    }

    public function processBasicFilter()
    {
        if (file_exists(APP_PATH . DS . "Shared" . DS . "auth_filters.php")) {
            require_once(APP_PATH . DS . "Shared" . DS . "auth_filters.php");
        }
        $filterObj = new \Auth_Filters;

        $UserType = isset($_SESSION["UserType"]) ? (int)$_SESSION["UserType"] : 0;
        switch ($UserType):
            case(1)://admin
                $filter = $filterObj->filters;
                $filter = $filter["admin"];
                if (in_array($this->controller, $filter)) {
                    return false;
                } elseif (in_array($this->controller . "@" . $this->method, $filter)) {
                    return false;
                } else {
                    return true;
                }
                break;
            case(2)://operator
                $filter = $filterObj->filters;
                $filter = $filter["operator"];
                if (in_array($this->controller, $filter)) {
                    return true;
                } elseif (in_array($this->controller . "@" . $this->method, $filter)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case(3)://user
                $filter = $filterObj->filters;
                $filter = $filter["user"];
                if (in_array($this->controller, $filter)) {
                    return true;
                } elseif (in_array($this->controller . "@" . $this->method, $filter)) {
                    return true;
                } else {
                    return false;
                }
                break;
            default://guest
                $filter = $filterObj->filters;
                $filter = $filter["guest"];
                if (!(in_array("Login", $filter) || in_array("login", $filter)))
                    dd("Login Filter must be in filter array");
                if (in_array($this->controller, $filter)) {
                    return true;
                } elseif (in_array($this->controller . "@" . $this->method, $filter)) {
                    return true;
                } else {
                    return false;
                }
                break;
        endswitch;
    }

    /**
     * This method creates new controller from the url and return
     * whatever the method specified by the url returns
     *
     * @return void
     */
    public function launch()
    {
        // Fix Controller name and append the '_Controller'
        $class = $this->controller;
        // Check if predefined Controller class exists
        if (file_exists(APP_PATH . DS . PANEL . DS . "Controllers" . DS . $class . 'Controller.php') && class_exists(PANEL . "\\" . "Controllers\\" . $class . 'Controller')) {
            $controller = PANEL . "\\" . "Controllers\\" . $class . 'Controller';
            $controller = new $controller;
        } // Check if predefined Model class exists and execute through CrudController
        elseif (file_exists(APP_PATH . DS . PANEL . DS . "Models" . DS . $class . '.php') && class_exists(PANEL . "\\" . "Models\\" . $class)) {
            $controller = "System\\MVC\\CrudController";
            $controller = new $controller($class, PANEL . "\\" . "Models\\" . $class);
        } else {
            // Controller doesn't exist
            // WebInterface error controller is created instead
            /*$controller = new \Error_Controller;

            // Call the index method
            return $controller->index();*/

            PageNotFound();

            var_dump("$class controller does not exists");
        }

        /*if(!$controller->restful)
            // If no restful then the method name is
            // prepended with 'action_' like laravel!*/
        $method = $this->method . "Action";

        /*
        else
        {
            // Restful is set to true so preppend the request name
            // ( POST, GET, PUT, DELETE, HEAD ) to the method
            $method = strtolower($_SERVER['REQUEST_METHOD'])."_" .$this->method;
        }*/

        // Check if the method exists in the controller
        if (method_exists($controller, $method)) {
            // Call the method giving the args array

            array_shift($this->args);


            call_user_func_array(array($controller, $method), $this->args);
        } else if ((method_exists($controller, "IndexAction")) && count($this->args) == 1) {
            call_user_func_array(array($controller, "IndexAction"), $this->args);
        } else {
            // Method doesn't exist
            // WebInterface error controller is created instead
            /*$controller = new \Error_Controller;

            // Call the index method
            return $controller->index();*/

            PageNotFound();

            var_dump("$method does not exists in controller $class");
        }
    }
}