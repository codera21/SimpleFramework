<?php

namespace Application\Config;

class ConnectionHelper extends DbConfig
{
    function __construct()
    {
        parent::__construct();
    }

    function dbConnect()
    {
        $db = new \PDO("mysql:host={$this->databaseConnection->ServerName};dbname={$this->databaseConnection->DatabaseName}"
            , $this->databaseConnection->Username, $this->databaseConnection->Password);

        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $db->exec("USE {$this->databaseConnection->DatabaseName};");

        return $db;
    }
}