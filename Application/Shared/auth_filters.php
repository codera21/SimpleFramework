<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2/15/2015
 * Time: 3:37 PM
 */
class Auth_Filters{

    public $filters = array(
        //controllers or methods that CANNOT be used by auth admin type

        "admin"=>array(""),
            //controllers or methods that CAN be used by auth operator type
        "operator"=>array("Login"),
            //controllers or methods that CAN be used by auth guest
        "user"=>array("Login"),
        //controllers or methods that CAN be used by no authorisation type
        "guest"=>array("Login")
        );

    public $intended = "NoAccess@index";
}
