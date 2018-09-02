<?php

namespace WebInterface\Controllers;

use System\MVC\Controller;

class PageNotFound extends Controller
{

    function IndexAction()
    {
        $this->load->TwigView("PageNotFound/Index");
    }

} 