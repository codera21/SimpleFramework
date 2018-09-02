<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 7/29/2016
 * Time: 10:40 PM
 */

namespace WebInterface\Controllers;


use System\MVC\Controller;

class HomeController extends Controller
{


    function IndexAction()
    {

        $this->load->TwigView("Home/index");

    }


}


