<?php

namespace System\Repositories;

use Application\Config\ConnectionHelper;
use Shared\Model\UserLog;
use WebInterface\Models;
use Infrastructure\SessionVariables;

class Repo
{
    protected $dbConnection;

    private $table;

    private $modelClass;

    function __construct($table, $modelClass)
    {
        $connectionHelper = new ConnectionHelper();

        $this->UserLogModel = new UserLog();

        $this->dbConnection = $connectionHelper->dbConnect();

        $this->table = $table;

        $this->modelClass = $modelClass;
    }

    protected function GetDbConnection()
    {
        return $this->dbConnection;
    }

    protected function Insert($model, $removeFields = array(), $table = null)
    {
        $modelArray = (array)$model;

        if ($table != null)
            $this->table = $table;

        foreach ($removeFields as $removeField) {
            unset($modelArray[$removeField]);
        }

        $insertSql = "INSERT INTO `{$this->table}` (";

        $keys = array_keys($modelArray);

        $insertSql .= '`' . implode('`,`', $keys) . '`' . ") ";

        $insertSql .= "VALUES(";

        $insertSql .= ":" . implode(',:', $keys) . ")";

        $sqlQuery = $this->GetDbConnection()->prepare($insertSql);

        foreach ($modelArray as $key => $value) {
            $sqlQuery->bindValue(":" . $key, $value);
        }
        $sqlQuery->execute();

        return $this->GetDbConnection()->lastInsertId();
    }

    protected function UpdateTable($model, $removeFields, $id = null, $table = null, $updateFrom = null, $updateFromValue = null)
    {
        $modelArray = (array)$model;

        foreach ($removeFields as $removeField) {
            unset($modelArray[$removeField]);
        }

        if ($table == null)
            $updateSql = "UPDATE `{$this->table}` SET ";
        else
            $updateSql = "UPDATE `$table` SET ";

        $keys = array_keys($modelArray);

        foreach ($keys as $key) {
            $updateSql .= "`$key`=:$key,";
        }

        $updateSql = rtrim($updateSql, ',');

        if ($updateFrom == null) {
            if ($id == null)
                $updateSql .= " WHERE ID=:ID";
            else
                $updateSql .= " WHERE $id=:$id";
        } else
            $updateSql .= " WHERE $updateFrom=:$updateFrom";

        $sqlQuery = $this->GetDbConnection()->prepare($updateSql);

        if ($updateFrom == null) {
            if ($id == null)
                $sqlQuery->bindValue(":ID", $model->ID);
            else
                $sqlQuery->bindValue(":$id", $model->$id);
        } else
            $sqlQuery->bindValue(":$updateFrom", $updateFromValue);

        foreach ($modelArray as $key => $value) {
            $sqlQuery->bindValue(":" . $key, $value);
        }
        $sqlQuery->execute();
    }


    public function GetCurrentDate()
    {
        $sqlQuery = $this->GetDbConnection()->query("SELECT CURRENT_DATE()");

        $date = $sqlQuery->fetchColumn();

        return $date;
    }

    public function GetCurrentDateTime()
    {
        $sqlQuery = $this->GetDbConnection()->query("SELECT CURRENT_TIMESTAMP");

        $datetime = $sqlQuery->fetchColumn();

        return $datetime;
    }

    public function Delete($id, $idFieldName = null)
    {
        try {
            if ($idFieldName == null) {
                $sqlQuery = $this->GetDbConnection()->prepare("DELETE FROM {$this->table} WHERE ID=:ID");

                $sqlQuery->bindValue("ID", $id);
            } else {
                $sqlQuery = $this->GetDbConnection()->prepare("DELETE FROM {$this->table} WHERE $idFieldName=:idFieldName");
                $sqlQuery->bindValue("idFieldName", $idFieldName);
            }

            $sqlQuery->execute();

            return true;
        } catch (\Exception $e) {

            return false;
        }
    }

    public function GetById($id, $idFieldName = null)
    {
        if ($idFieldName == null) {
            $sqlQuery = $this->GetDbConnection()->prepare("SELECT * FROM `{$this->table}` WHERE ID=:ID");
            $sqlQuery->bindParam(':ID', $id);
        } else {
            $sqlQuery = $this->GetDbConnection()->prepare("SELECT * FROM `{$this->table}` WHERE $idFieldName=:$idFieldName");
            $sqlQuery->bindParam(":$idFieldName", $id);
        }
        $sqlQuery->execute();

        $model = new $this->modelClass();

        while ($row = $sqlQuery->fetch(\PDO::FETCH_ASSOC)) {
            $model->MapParameters($row);
        }
        return $model;
    }

    public function GetAllByViewModelWithOutJoin($viewModelClass, $whereConditions = array())
    {
        $viewModel = new $viewModelClass();

        $viewModelArray = (array)$viewModel;

        $keys = array_keys($viewModelArray);

        $sql = "SELECT ";

        foreach ($keys as $key) {
            $sql .= " [$key],";
        }

        $sql = rtrim($sql, ',');

        $sql .= " FROM `{$this->table}`";

        if (count($whereConditions) != 0) {
            $sql .= " WHERE 1 ";

            foreach ($whereConditions as $whereCondition) {
                $sql .= " AND $whereCondition`Field`=$whereCondition[Match]";
            }
        }

        $list = array();

        $sqlQuery = $this->GetDbConnection()->query($sql);

        foreach ($sqlQuery->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $viewModel = new $viewModelClass();
            $viewModel->MapParameters($row);

            array_push($list, $viewModel);
        }

        return $list;
    }

    public function GetAll()
    {
        $sqlQuery = $this->GetDbConnection()->query("SELECT * FROM `{$this->table}`");

        $list = array();

        foreach ($sqlQuery->fetchAll(\PDO::FETCH_ASSOC) as $row) {

            $model = new $this->modelClass();

            $model->MapParameters($row);

            array_push($list, $model);
        }
        return $list;
    }

    function Check($id)
    {
        $sql = 'SELECT Count(*) FROM `{$this->table}` WHERE ID=:ID';

        $sqlQuery = $this->dbConnection->prepare($sql);

        $sqlQuery->bindValue(":ID", $id);

        $sqlQuery->execute();

        return $sqlQuery->fetch(\PDO::FETCH_COLUMN) == 0 ? false : true;
    }
}