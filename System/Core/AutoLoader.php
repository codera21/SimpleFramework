<?php

class AutoLoader
{

    /**
     * load class file by it's full name
     *
     * @param string $class
     * @return void
     */
    public static function Load($uri)
    {
        $class = str_replace('\\', DS, $uri);
        var_dump($class);

        if (file_exists($class . '.php')) //BASE_PATH.DS.
        {
            require_once($class . '.php');
        } elseif (file_exists(LANG_PATH . DS . $class . '.php')) {
            require_once(LANG_PATH . DS . $class . '.php');
        } elseif (file_exists(LANG_PATH . DS . "English" . $class . '.php')) {
            require_once(LANG_PATH . DS . $class . '.php');
        } elseif (file_exists(LANG_PATH . DS . "Russian" . $class . '.php')) {
            require_once(LANG_PATH . DS . $class . '.php');
        } elseif (file_exists(APP_PATH . DS . $class . '.php')) {
            require_once(APP_PATH . DS . $class . '.php');
        } elseif (file_exists(BASE_PATH . DS . $class . '.php')) {
            require_once(BASE_PATH . DS . $class . '.php');
        } elseif (file_exists(BASE_PATH . DS . APP_PATH . DS . $class . '.php'))//BASE_PATH.DS.
        {
            require_once($class . '.php');
        } else {
            var_dump("$class.php");
        }
    }
}