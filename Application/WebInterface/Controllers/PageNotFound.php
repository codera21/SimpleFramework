<?php

namespace WebInterface\Controllers;

use System\MVC\Controller;

class PageNotFound extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function IndexAction()
    {
        $this->load->TwigView("PageNotFound/Index");
    }

} 