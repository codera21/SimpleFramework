# SimpleFramework
It is a MVC framwork made in PHP.

## Requirement 
Since I am using latest version of twig and it needs at least php 7.0. 
you will need what a typical php project will require. 
   
## Installation 
1. clone this repository 
2. run composer update 
3. run npm install


### Using the Controller
 Inside `Application\WebInterface`you will see three folders `Controllers` , `Models` , `ViewModels` , `Views`. Goto Controllers then Create one new Controller Let's Say `PageController.php`  with one method `IndexAction()` the code is as shown below:
```
<?php 
namespace WebInterface\Controllers;
use System\MVC\Controller;

class PageController extends Controller
{
    public function IndexAction()
    {
        echo("Hello World");
    }
}
```
Then in the output navigate to `<Host>/<ProjectName>/Page/Index` or `<Host>/<ProjectName>/Page` in my case `http://localhost:90/SimpleFrameWork/Page` then you will see the page with string "Hello World" echoed out. 
Yes the routes are created automatically in this format : `<host>/<controller>/<action/method>`

##### Parameters
In the same PageController I created a new method AddAction(int $num1 , int $num2) the code is as follows: 
```
    public function AddAction($num1, $num2)
    {
        echo $num1 + $num2;
    }
```
Now if you navigate to link like `http://localhost:90/SimpleFrameWork/Page/Add/5/5` then you will see in the page 10 echoed out.
### Using the view 
Twig  is used as templating language for the framework 
The base twig file is in `Application/Shared/View/base.twig` you will see the base html with common libaries like bootstraps, jquery, etc. are installed already. 
Let's make a new folder called `Page` in the `WebInterface/Views` and make a new file index.twig with following code:
```
{% extends 'base.twig' %}
{% block content %}
    <p class="text-center">{{ data }}</p>
{% endblock %}
```
In the IndexAction of PageController the code to load view as shown below:
```
public function IndexAction()
{
 $data = "Hello World !";
 $this->load->TwigView('Page/index', ['data' => $data]);
}
```
### Dealing with database
One of the strong suite of the SimpleFramework is its easy to use out of the box ORM for common database queries:

#### Connection to database 
Goto `Application/Config/DbConfig.php` and fill up the ServerName, Username, Password and DatabaseName. 
eg: 
```
   function __construct()
    {
        $this->databaseConnection = new DatabaseConnection();

        $this->databaseConnection->ServerName = 'localhost';
        $this->databaseConnection->Username = 'root';
        $this->databaseConnection->Password = '';
        $this->databaseConnection->DatabaseName = 'products';
    }
```
### Model 
Suppose you have a table named items with columns ID , ItemName, ItemPrice, ItemCategory. Then in `WebInterface/Models` create a class `Items` like: 
```
<?php
namespace WebInterface\Models;

use System\MVC\ModelAbstract;

class Items extends ModelAbstract
{
    public $ID;
    public $ItemName;
    public $ItemPrice;
    public $ItemCategory;
}
```
### Repository 
Make a Repo of database functions in `Application/Repository` make a new class ItemRepo with constructor initialized with Model class ( made above) and table like this:
```
<?php
use System\Repositories\Repo;

class ItemRepo extends Repo
{
    private $table = 'items';
    private $modelClass = 'WebInterface\\Models\\Items';

    public function __construct()
    {
        parent::__construct($this->table, $this->modelClass);
    }
}
```
### Calling Common database queries:
In the PageController's `IndexAction` I can create an object of `ItemRepo` and use its common queries:
```
 public function IndexAction()
    {
        $itemRepo = new ItemRepo();
        $itemData = $itemRepo->GetAll();
        $this->load->TwigView('Page/index', ['data' => $itemData]);
    }
```
It is a good idea to make object in the constructor if more than one function uses the particular Repository: 
```
<?php
namespace WebInterface\Controllers;

use Repositories\ItemRepo;
use System\MVC\Controller;

class PageController extends Controller
{
    private $itemRepo;
    public function __construct()
    {
        parent::__construct();
        $this->itemRepo = new ItemRepo();
    }
    public function IndexAction()
    {
        $itemData = $this->itemRepo->GetAll();
        $this->load->TwigView('Page/index', ['data' => $itemData]);
    }
}
```

#### list of common queries 
```
 Insert($model, $removeFields = array(), $table = null) // this is protected function
 UpdateTable($model, $removeFields, $id = null, $table = null, $updateFrom = null, $updateFromValue = null) // this is also protected
 GetCurrentDate()
 GetCurrentDateTime()
 Delete($id, $idFieldName = null)
 GetById($id, $idFieldName = null)
 GetAllByViewModelWithOutJoin($viewModelClass, $whereConditions = array())
 GetAll()
 Check($id)
```
#### custom query ( we use PDO)
you can create your own custom functions and make your own queries like this in `Repository/ItemRepo` new function `Custom` is made:
```
public function Custom($id)
    {
        $sql = "select * from items where ID = :ID";
        $sqlQuery = $this->dbConnection->prepare($sql);
        $sqlQuery->bindParam(':ID', $id);
        $sqlQuery->execute();
    }
```
## Gulp 
It has gulp out of the box. Just run `npm run runserver` if you have gulp installed locally
## Npm 
It uses npm out of the box. Just install javascript modules by `npm install module-name` and include it as the example below:
```
<script type="text/javascript" src="{{ MODULE }}jquery/dist/jquery.js"></script>
```
#### Many More Features
There are many more features of SimpleFramework, that is not covered here. please go through this framework and see for yourself :) 
