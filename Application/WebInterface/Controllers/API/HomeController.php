<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 12/5/2018
 * Time: 9:09 PM
 */

namespace WebInterface\Controllers\API;


use System\MVC\Controller;

class HomeController extends Controller
{
    public function IndexAction()
    {
        echo("Hello World From API");
    }

}