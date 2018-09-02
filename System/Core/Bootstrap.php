<?php
session_start();
require_once 'vendor/autoload.php';

// new feature auto loading  write like this
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DS, $class);
    if (file_exists($class . '.php')) {
        require_once($class . '.php');
    } elseif (file_exists(APP_PATH . DS . $class . '.php')) {
        require_once(APP_PATH . DS . $class . '.php');
    } elseif (file_exists(BASE_PATH . DS . $class . '.php')) {
        require_once(BASE_PATH . DS . $class . '.php');
    } elseif (file_exists(BASE_PATH . DS . APP_PATH . DS . $class . '.php')) {
        require_once(BASE_PATH . DS . APP_PATH . DS . $class . '.php');
    } else {
        echo "Error::$class.php not found";
    }
});

/**
 * Load system and user configuration options
 */
$objConfig = new System\Core\Config();
$AppConfig = $objConfig->GetConfig();
/*
 *  Load the global functions
 */
require_once('Common.php');
$uri = $_SERVER['REQUEST_URI'];
$directory = str_replace('/index.php', "", substr($uri, 0, strpos($_SERVER['PHP_SELF'], '/index.php')));
$uri = str_replace($directory, "", $uri);
$uri = str_replace('index.php', "", $uri);
$uri = strpos($uri, '?') ? substr($uri, 0, strpos($uri, '?')) : $uri;

if ($uri != "/") {
    if (strpos($uri, $AppConfig['AdminFolderSecureName'])) {
        $uri = str_replace($AppConfig['AdminFolderSecureName'], "", $uri);
        $AppConfig['isAdmin'] = true;
        define('PANEL', $AppConfig['AdminFolder']);
        define('ADMIN_FOLDER_SECURE_NAME', $AppConfig['AdminFolderSecureName']);
    }
}

if (!defined('PANEL'))
    define('PANEL', $AppConfig['WebInterfaceFolder']);

$router = new System\Core\Router($AppConfig);

if ($uri != "/") {
    $router->pathRoute($uri);
} else {
    if ($AppConfig['isAdmin'])
        $router->adminDefaultRoute();
    else
        $router->interfaceDefaultRoute();
}

// install twig loader
$loader = new Twig_Loader_Filesystem(array(BASE_PATH, BASE_PATH . DS . APP_PATH . DS . "Shared" . DS . "Views" . DS));
$twig = new Twig_Environment($loader, array(
    'debug' => true,
    // ...
));
$twig->addExtension(new Twig_Extension_Debug());

$router->launch();
