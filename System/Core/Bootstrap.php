<?php
session_start();
require_once 'vendor/autoload.php';

// new feature auto loading  write like this
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DS, $class);
    if (file_exists($class . '.php')) {
        require_once ($class . '.php');
    } elseif (file_exists(APP_PATH . DS . $class . '.php')) {
        require_once (APP_PATH . DS . $class . '.php');
    } elseif (file_exists(BASE_PATH . DS . $class . '.php')) {
        require_once (BASE_PATH . DS . $class . '.php');
    } elseif (file_exists(BASE_PATH . DS . APP_PATH . DS . $class . '.php')) {
        require_once (BASE_PATH . DS . APP_PATH . DS . $class . '.php');
    } else {
        echo "Error::$class.php not found";
    }
});

/**
 * Load system and user configuration options
 */
$objConfig = new System\Core\Config();
$AppConfig = $objConfig->GetConfig();
$AppConfig['isAdmin'] = false;
$AppConfig['isAPI'] = false;
/*
 *  Load the global functions
 */
require_once 'Common.php';
$uri = $_SERVER['REQUEST_URI'];
$directory = str_replace('/index.php', "", substr($uri, 0, strpos($_SERVER['PHP_SELF'], '/index.php')));
$uri = str_replace($directory, "", $uri);
$uri = str_replace('index.php', "", $uri);
$uri = strpos($uri, '?') ? substr($uri, 0, strpos($uri, '?')) : $uri;

if ($uri != "/") {
    if (stripos($uri, $AppConfig['AdminFolderSecureName']) !== false) {
        $uri = preg_replace('/' . $AppConfig['AdminFolderSecureName'] . '/', "", $uri, 1);
        $AppConfig['isAdmin'] = true;
        define('PANEL', $AppConfig['AdminFolder']);
        define('ADMIN_FOLDER_SECURE_NAME', $AppConfig['AdminFolderSecureName']);
    }
}

if (!defined('PANEL')) {
    define('PANEL', $AppConfig['WebInterfaceFolder']);
}

$router = new System\Core\Router($AppConfig);

if ($uri != "/") {
    $router->pathRoute($uri);
} else {
    if ($AppConfig['isAdmin']) {
        $router->adminDefaultRoute();
    } else {
        $router->interfaceDefaultRoute();
    }

}

$router->launch();
