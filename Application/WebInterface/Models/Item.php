<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 9/2/2018
 * Time: 11:12 PM
 */

namespace WebInterface\Models;


use System\MVC\ModelAbstract;

class Item extends ModelAbstract
{
    public $ID;
    public $ItemName;
    public $ItemPrice;
    public $ItemCategory;
}