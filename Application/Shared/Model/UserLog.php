<?php
/**
 * Created by PhpStorm.
 * User: Love Shankar Shresth
 * Date: 2/26/2015
 * Time: 12:53 PM
 */

namespace Shared\Model;

use System\MVC\ModelAbstract;

class UserLog extends ModelAbstract {

    public $ID;

    public $DateTime;

    public $UserID;

    public $UserActions;

    public $UserActionsRussian;
}