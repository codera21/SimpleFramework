<?php
session_start();
require_once('AutoLoader.php');
require_once 'vendor/autoload.php';

spl_autoload_register(array('AutoLoader', 'Load'));

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
// remove directory name if any
$directory = str_replace('/index.php', "", substr($uri, 0, strpos($_SERVER['PHP_SELF'], '/index.php')));
$uri = str_replace($directory, "", $uri);
// remove index.php if any
$uri = str_replace('index.php', "", $uri);
// remove get param i.e take only string before ?
$uri = strpos($uri, '?') ? substr($uri, 0, strpos($uri, '?')) : $uri;

if ($uri != "/") {
    // check if $uri is pointing towards admin section
    if (strpos($uri, $AppConfig['AdminFolderSecureName'])) {
        $uri = str_replace($AppConfig['AdminFolderSecureName'], "", $uri);
        $AppConfig['isAdmin'] = true;
        define('PANEL', $AppConfig['AdminFolder']);
        define('ADMIN_FOLDER_SECURE_NAME', $AppConfig['AdminFolderSecureName']);
    }
}

if (!defined('PANEL'))
    define('PANEL', $AppConfig['WebInterfaceFolder']);

/**
 * Get a router instance and route giving the PHP_INFO if
 * is set then call the launch method to get an instance of
 * the Controller and call the specified method with the args
 */
$router = new System\Core\Router($AppConfig);

if ($uri != "/") {
    // route depending on the path
    $router->pathRoute($uri);
} else {
    // Route to defualt route in the Config
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
