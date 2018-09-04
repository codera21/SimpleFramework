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

$serverType = "http://";

if (is_ssl()) {
    $serverType = "https://";
}

define('BASE_URL', $serverType . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));

define('APP_FOLDER', 'Application');

define('SYSTEM_FOLDER', 'System');

define('LANG_FOLDER', 'Language');


define('APP_PATH', APP_FOLDER);

define('LANG_PATH', LANG_FOLDER);

define('SYSTEM_PATH', SYSTEM_FOLDER);

require_once(SYSTEM_PATH . DS . 'Core/Bootstrap.php');

