<?php
namespace WebInterface\Controllers;

use System\MVC\Controller;

class HomeController extends Controller
{
    function IndexAction()
    {
      try {
          $this->load->TwigView('Home/index');
      } catch (\Exception $e) {
          var_dump("Error :" . $e);
      }
    }

}
