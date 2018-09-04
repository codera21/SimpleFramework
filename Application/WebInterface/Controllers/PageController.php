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


        try {
            $this->load->TwigView('Page/index');
        } catch (\Exception $e) {
            var_dump("Error :" . $e);
        }
    }

}