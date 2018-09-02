<?php

namespace Application\Config;

use WebInterface\Models\DatabaseConnection;

class DbConfig
{
    protected $databaseConnection;

    function __construct()
    {
        $this->databaseConnection = new DatabaseConnection();

        $this->databaseConnection->ServerName = 'localhost';
        $this->databaseConnection->Username = 'root';
        $this->databaseConnection->Password = '';
        $this->databaseConnection->DatabaseName = '';
    }
}