<?php

namespace System\Core;

use Admin\Controllers\AdminShared;
use Infrastructure\SessionVariables;
use Repositories\LoginUserLogRepository;
use Repositories\UserPermissionRepository;
use WebInterface\Controllers\WebInterfaceShared;

class Loader
{
    public $site_js;

    function __construct()
    {

    }

    function Library($class, $directory = NULL)
    {
        foreach (array(APP_PATH, SYSTEM_PATH) as $path) {
            $file = $path . DS . 'Libraries' . DS . $directory . DS . $class . '.php';
//echo '<br/>'.$file;exit;
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
        //$viewFile = str_replace("/", "\\", $viewFile);
        $filePath = APP_PATH . DS . PANEL . DS . 'Views' . DS . $viewFile . '.php';
        //var_dump($filePath);exit;
        if (file_exists($filePath)) {
            if (!empty($data)) extract($data);

            include_once($filePath);

        } else
            throw new \Exception("Unable to load the requested view file: $viewFile.php");
    }

    function LoadJS($jsArray)
    {
        $this->site_js = "";
        if (count($jsArray) > 0) {
            foreach ($jsArray as $jsPath) {
                if (file_exists($jsPath)) {
                    $this->site_js .= "<script src='" . BASE_URL . "/" . $jsPath . "'></script>\n";
                }
            }
        }
        return $this->site_js;
    }

    function TwigView($viewFile, $data = array())
    {
        global $twig;


        $filePath = APP_PATH . DS . PANEL . DS . 'Views' . DS . $viewFile . '.twig';


//        $userLogRepository = new LoginUserLogRepository();

//        $currentTimeStamp = $userLogRepository->GetCurrentDateTime();
//        $currentDate = $userLogRepository->GetCurrentDate();

        if (file_exists($filePath)) {

            $twigTemplate = $twig->loadTemplate($filePath);

            $data["AppPath"] = APP_PATH . DS;
            $data["BASE_URL"] = BASE_URL;
            $data["SERVER"] = $_SERVER;
            $data["SESSION"] = $_SESSION;
            $data["POST"] = $_POST;
            $data["GET"] = $_GET;
            $data["ViewFile"] = $viewFile;
            $wShared = new WebInterfaceShared();
            if ($wShared->Shared()) {
                $data["Shared"] = $wShared->Shared();
            }
            $aShared = new AdminShared();
            if ($aShared->Shared()) {
                $data["AdminShared"] = $aShared->Shared();
            }


//            if (isset($_SESSION["UserType"])) {
//                $userPermissionRepository = new UserPermissionRepository();
//                $userPageAccess = $userPermissionRepository->GetUserAccess($_SESSION["UserType"]);
//                $data["UserPageAccess"] = $userPageAccess;
//            }
//
//
            $template = $twigTemplate->render($data);
            echo $template;
//            $_SESSION[SessionVariables::$ConfirmationMessage] = "";
//            $_SESSION[SessionVariables::$ConfirmationMessageType] = "";
        } else
            throw new \Exception("Unable to load the requested view file: $viewFile.twig");
    }

    /*Generic File*/
    function File($path)
    {
        if (file_exists($path)) {
            include_once($path);
        } else
            throw new \Exception("Unable to load the requested file");
    }

}