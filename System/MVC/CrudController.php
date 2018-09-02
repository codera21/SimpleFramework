<?php

namespace System\MVC;

use System\Repositories\CrudRepository;
use Shared\Model\AjaxGrid;


class CrudController extends Controller
{
    private $repository;
    private $modelClass;
    protected $model;
    private $excludedField;

    function __construct($modelName, $Model)
    {
        parent::__construct();

        $this->model = new $Model();

        $this->modelClass = $modelName;

        $this->repository = new CrudRepository($Model);

        $this->excludedField = array("TableName", "PrimaryKey", "Validation", "Fields");
    }

    function IndexAction($fileName = null, $param = null)
    {

        if ($_POST) {
            $this->model->MapParameters($_POST);

            $pk = "ID";
            if (isset($this->model->PrimaryKey)) {
                $pk = $this->model->PrimaryKey;
            }

            if (isset($this->model->$pk) && $this->model->$pk != "") {
                $this->repository->Update($this->model, $this->excludedField, $pk, $this->model->TableName);
            } else {

                $this->repository->Save($this->model, $this->excludedField, $this->model->TableName);
            }

        }


        if ($param == null && $fileName != null)
            $this->load->TwigView($fileName);
        else if ($param != null && $fileName == null)
            $this->load->TwigView('AutoDashboard/index', $param);
        else if ($param != null && $fileName != null)
            $this->load->TwigView($fileName, $param);
        else if ($param == null && $fileName == null)
            $this->load->TwigView('AutoDashboard/index');

    }

    function FormAction($filename = null, $param = null)
    {
        if (isset($_GET['ID'])) {

            $param['Data'] = $this->repository->GetById($_GET['ID']);


        }
        $param['Fields'] = $this->GetFields();


        if ($filename == null)
            $this->load->TwigView('AutoDashboard/form', $param);
        else
            $this->load->TwigView($filename, $param);

    }

    function ListAction()
    {
        $ajaxGrid = new AjaxGrid();

        $ajaxGrid->MapParameters($_GET);

        echo json_encode($this->repository->FindAll($ajaxGrid));
    }

    function DeleteAction($redirectAddress = null)
    {


        if (isset($_POST['ID']) && $_POST['ID'] > 0) {
            $id = $_POST['ID'];
            var_dump($this->repository->DeleteFromID($id));
        }
        if ($redirectAddress == null)
            Redirect(SECURE_ADMIN_FOLDER_NAME . "/AutoDashboard?menu_item=" . $this->modelClass);
        else
            Redirect(SECURE_ADMIN_FOLDER_NAME . '/' . $redirectAddress);

    }


    function GetFields()
    {
        return $this->model->Fields;

    }

}