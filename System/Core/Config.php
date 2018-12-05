<?php

namespace System\Core;

class Config
{
    private $config;


    function __construct()
    {
        $this->config = array();
        $this->initialize();
    }

    /**
     * Load user Configurations
     *
     * @return void
     */
    private function initialize()
    {
        $this->config['AdminFolderSecureName'] = "admin";
        $this->config['AdminFolder'] = "Admin";
        $this->config['WebInterfaceFolder'] = "WebInterface";
        $this->config['InterfaceDefaultRoute'] = array('Controller' => 'Home', 'Action' => 'Index');
        $this->config['AdminDefaultRoute'] = array('Controller' => 'Dashboard', 'Action' => 'Index');
    }

    function GetConfig()
    {
        return $this->config;
    }

}