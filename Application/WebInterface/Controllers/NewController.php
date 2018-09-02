<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 3/25/2017
 * Time: 2:40 PM
 */

namespace WebInterface\Controllers;


use System\MVC\Controller;

class NewController extends Controller
{

    function IndexAction()
    {
        $this->load->TwigView("New/index");
    }





}