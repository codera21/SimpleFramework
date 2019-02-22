<?php

namespace System\Repositories;

use Application\Config\ConnectionHelper;
use Shared\Model\AjaxGrid;
use WebInterface\Models;
use Infrastructure\SessionVariables;

class Repo
{
    protected $dbConnection;

    private $table;

    private $modelClass;

    public function __construct($table, $modelClass)
    {
        $connectionHelper = new ConnectionHelper();

        $this->dbConnection = $connectionHelper->dbConnect();

        $this->table = $table;

        $this->modelClass = $modelClass;
    }

    protected function GetDbConnection()
    {
        return $this->dbConnection;
    }

    public function Insert($model, $removeFields = array(), $table = null)
    {
        $modelArray = (array)$model;

        if ($table != null) {
            $this->table = $table;
        }

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

    public function UpdateTable($model, $removeFields, $id = null, $table = null, $updateFrom = null, $updateFromValue = null)
    {
        $modelArray = (array)$model;

        foreach ($removeFields as $removeField) {
            unset($modelArray[$removeField]);
        }

        if ($table == null) {
            $updateSql = "UPDATE `{$this->table}` SET ";
        } else {
            $updateSql = "UPDATE `$table` SET ";
        }

        $keys = array_keys($modelArray);

        foreach ($keys as $key) {
            $updateSql .= "`$key`=:$key,";
        }

        $updateSql = rtrim($updateSql, ',');

        if ($updateFrom == null) {
            if ($id == null) {
                $updateSql .= " WHERE ID=:ID";
            } else {
                $updateSql .= " WHERE ID=:$id";
            }
        } else {
            $updateSql .= " WHERE $updateFrom=:$updateFrom";
        }

        $sqlQuery = $this->GetDbConnection()->prepare($updateSql);

        if ($updateFrom == null) {
            if ($id == null) {
                $sqlQuery->bindValue(":ID", $model->ID);
            } else {
                $sqlQuery->bindValue(":$id", $model->$id);
            }
        } else {
            $sqlQuery->bindValue(":$updateFrom", $updateFromValue);
        }

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

    public function Check($id)
    {
        $sql = 'SELECT Count(*) FROM `{$this->table}` WHERE ID=:ID';

        $sqlQuery = $this->dbConnection->prepare($sql);

        $sqlQuery->bindValue(":ID", $id);

        $sqlQuery->execute();

        return $sqlQuery->fetch(\PDO::FETCH_COLUMN) == 0 ? false : true;
    }


    public function AjaxGridPaginate(AjaxGrid $ajaxGrid, $filter = null, $table = null)
    {
        if ($table == null) {
            $table = $this->table;
        }
        $modalObj = new $this->modelClass;

        if ($filter == null) {
            $sql = "SELECT * FROM `$table` ORDER BY $ajaxGrid->sortExpression $ajaxGrid->sortOrder
            LIMIT $ajaxGrid->offset,$ajaxGrid->rowNumber";
        } else {
            $sql = "SELECT * FROM `$table` WHERE ";
            foreach ($modalObj as $key => $value) {
                $sql .= "`$key` LIKE '%$filter%'  OR ";
            }
            $sql = rtrim($sql, 'OR ');
            $sql .= " ORDER BY $ajaxGrid->sortExpression $ajaxGrid->sortOrder LIMIT $ajaxGrid->offset,$ajaxGrid->rowNumber";
        }

        $sqlQuery = $this->GetDbConnection()->query($sql);
        $data = $sqlQuery->fetchAll(\PDO::FETCH_ASSOC);
        if ($filter == null) {
            $sqlQuery = $this->GetDbConnection()->query("SELECT Count(*) FROM {$table}");
        } else {
            $sql = "SELECT Count(*) FROM `$table` WHERE ";
            foreach ($modalObj as $key => $value) {
                $sql .= "`$key` LIKE '%$filter%'  OR ";
            }
            $sql = rtrim($sql, 'OR ');
            $sqlQuery = $this->GetDbConnection()->query($sql);
        }
        $rowCount = $sqlQuery->fetch();
        $list['RowCount'] = $rowCount[0];
        $list['Data'] = $data;
        $list['PageNumber'] = $ajaxGrid->pageNumber;
        return json_encode($list);
    }

    public function GenerateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}
