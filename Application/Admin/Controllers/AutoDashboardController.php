<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 11/25/2016
 * Time: 7:54 PM
 */

namespace Admin\Controllers;


use System\MVC\Controller;
use System\MVC\CrudController;


class AutoDashboardController extends Controller
{


    function IndexAction()
    {


        // Make Nav Menu by scanning through Models folder
        $models = scandir(APP_PATH . '/Admin/Models');
        $param['NavMenu'] = array();
        foreach ($models as $m) {
            if ($m != '.' && $m != '..') {
                //Remove .php from the file name
                $m = substr($m, 0, -4);
                array_push($param['NavMenu'], $m);
            }
        }

        if (!empty($param['NavMenu'])) {
            // show content according to get request
            if (!isset($_GET['menu_item'])) {
                $menuItem = $param['NavMenu'][0];
            } else if (!in_array($_GET['menu_item'], $param['NavMenu'])) {
                $menuItem = $param['NavMenu'][0];
            } else {
                $menuItem = $_GET['menu_item'];
            }
            $autoAdmin = new   CrudController($menuItem, 'Admin\\Models\\' . $menuItem);
            //get  the columns of the table
            $param['NowItem'] = $menuItem;
            $param['TableHeading'] = array_keys($autoAdmin->GetFields());

            $param['Url'] = BASE_URL . SECURE_ADMIN_FOLDER_NAME . '/' . $menuItem . '/';
            $autoAdmin->IndexAction(null, $param);
        } else {
            die ('No  Models Created So Far');
        }
    }


}

