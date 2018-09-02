<?php
error_reporting(E_ALL);
function is_ssl()
{
    if (isset($_SERVER['HTTPS'])) {
        if ('on' == strtolower($_SERVER['HTTPS']))
            return true;
        if ('1' == $_SERVER['HTTPS'])
            return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

define('DS', DIRECTORY_SEPARATOR);

define('BASE_PATH', dirname(__FILE__));

// define('BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));

$serverType = "http://";

if (is_ssl()) {
    $serverType = "https://";
}

define('BASE_URL', $serverType . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));


define('APP_FOLDER', 'Application');

define('SYSTEM_FOLDER', 'System');

define('LANG_FOLDER', 'Language');

/*The path to the "Application" folder*/
//define('APP_PATH', BASE_PATH . DS . APP_FOLDER);
define('APP_PATH', APP_FOLDER);

define('LANG_PATH', LANG_FOLDER);

/*The path to the "System" folder*/
define('SYSTEM_PATH', SYSTEM_FOLDER);

define('INCLUDE_URL', BASE_URL . 'includes');


require_once(SYSTEM_PATH . DS . 'Core/Bootstrap.php');

