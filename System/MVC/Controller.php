<?php

namespace System\MVC;

use Infrastructure\SessionVariables;
use Repositories\UserOnlineRepository;
use System\Core\Loader;

abstract class Controller
{
    private static $instance;
    protected $language;

    public function __construct()
    {
        self::$instance =& $this;
        $this->load = new Loader();

        if (!ob_get_status()) {

            if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
                ob_start('ob_gzhandler');
            else
                ob_start();
        }

        $this->CheckOnline();
    }

    function CheckOnline()
    {
        if (isset($_SESSION[SessionVariables::$UserID]) && $_SESSION[SessionVariables::$UserID] > 0) {
            $userOnlineRepository = new UserOnlineRepository();

            $userOnlineRepository->UpdateOnlineTime($_SESSION[SessionVariables::$UserID]);
        }
    }

}