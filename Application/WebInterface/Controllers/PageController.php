<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 9/2/2018
 * Time: 11:30 PM
 */

namespace WebInterface\Controllers;


use System\MVC\Controller;

class PageController extends Controller
{
    public function IndexAction()
    {
        echo("Page/Index");
    }

}