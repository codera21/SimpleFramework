<?php

namespace System\Core;


use Infrastructure\TwigShared;

class Loader
{
    function Library($class, $directory = NULL)
    {
        foreach (array(APP_PATH, SYSTEM_PATH) as $path) {
            $file = $path . DS . 'Libraries' . DS . $directory . DS . $class . '.php';
            if (file_exists($file)) {
                if (class_exists($file) === FALSE) {
                    require_once($file);
                    return;
                } else
                    throw new \Exception("Unable to load the requested class: $class");
            } else
                throw new \Exception("Unable to load the requested class file: $class.php");
        }
    }

    function View($viewFile, $data = array())
    {
        $filePath = APP_PATH . DS . PANEL . DS . 'Views' . DS . $viewFile . '.php';
        if (file_exists($filePath)) {
            if (!empty($data)) extract($data);
            include_once($filePath);
        } else
            throw new \Exception("Unable to load the requested view file: $viewFile.php");
    }

    function TwigView($viewFile, $data = array())
    {
        global $twig;
        $filePath = APP_PATH . DS . PANEL . DS . 'Views' . DS . $viewFile . '.twig';
        if (file_exists($filePath)) {
            $twigTemplate = $twig->loadTemplate($filePath);
            $data["AppPath"] = APP_PATH . DS;
            $data["BASE_URL"] = BASE_URL;
            $data["SERVER"] = $_SERVER;
            $data["SESSION"] = $_SESSION;
            $data["COOKIE"] = $_COOKIE;
            $data["POST"] = $_POST;
            $data["GET"] = $_GET;
            $data["ViewFile"] = $viewFile;
            $data['redirect_link'] = $_SERVER["REQUEST_URI"];
            $data['Shared'] = TwigShared::$data;

            $template = $twigTemplate->render($data);
            echo $template;
        } else
            throw new \Exception("Unable to load the requested view file: $viewFile.twig");
    }

    // generic file
    function File($path)
    {
        if (file_exists($path)) {
            include_once($path);
        } else
            throw new \Exception("Unable to load the requested file");
    }
}