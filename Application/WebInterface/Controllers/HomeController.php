<?php

namespace WebInterface\Controllers\API;

use System\MVC\Controller;

class HomeController extends Controller
{
    public function IndexAction()
    {
        $ret = json_encode(['status' => 200, 'homePageCollection' => "OK"]);
        echo ($ret);
    }

}
