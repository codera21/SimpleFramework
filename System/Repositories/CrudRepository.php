<?php

namespace System\Repositories;


use System\Repositories\Repo;
use Shared\Model\AjaxGrid;

class CrudRepository extends Repo
{
    private $modelClass;
    private $modelObject;


    function __construct($modelClass)
    {
        parent::__construct(null, null);
        $this->modelClass = $modelClass;
        $this->modelObject = new $this->modelClass();
    }


    function FindAll(AjaxGrid $ajaxGrid)
    {
        $sql = "SELECT * FROM {$this->modelObject->TableName} ORDER BY $ajaxGrid->sortExpression $ajaxGrid->sortOrder LIMIT $ajaxGrid->offset,$ajaxGrid->rowNumber";


        $sqlQuery = $this->dbConnection->query($sql);

        $data = $sqlQuery->fetchAll(\PDO::FETCH_ASSOC);

        $sqlQuery = $this->dbConnection->query("SELECT Count(*) FROM {$this->modelObject->TableName}");
        $rowCount = $sqlQuery->fetch();

        $list['RowCount'] = $rowCount[0];
        $list['Data'] = $data;
        $list['PageNumber'] = $ajaxGrid->pageNumber;

        return $list;
    }

    function Save($model, $removeFields = array(), $table)
    {
        $this->Insert($model, $removeFields, $table);

    }

    function Update($model, $removeFields = null, $id, $table, $updateFrom = null, $updateFromValue = null)
    {

        $this->UpdateTable($model, $removeFields, $id, $table, $updateFrom, $updateFromValue);

    }

    function DeleteFromID($id)
    {
        $sqlQuery = $this->GetDbConnection()->prepare("DELETE FROM {$this->modelObject->TableName} WHERE ID=:ID");

        $sqlQuery->bindValue("ID", $id);

        $sqlQuery->execute();
    }

    function GetById($id, $idFieldName = null) //second parameter to avoid warning in  strict mode
    {
        $sqlQuery = $this->dbConnection->prepare("SELECT * FROM {$this->modelObject->TableName} WHERE ID=:Id");
        $sqlQuery->bindParam(':Id', $id);
        $sqlQuery->execute();
        return $sqlQuery->fetch(\PDO::FETCH_ASSOC);

    }


}