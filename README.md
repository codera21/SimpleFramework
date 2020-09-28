# SimpleFramework
It is a MVC framwork made in PHP.

## Requirement 
Since I am using latest version of twig and it needs at least php 7.0. 
you will need what a typical php project will require. 
   
## Installation
run: 
- `composer create-project codera21/sf project_name`
- `cd project_name`
- `run php -S localhost:8080`

## Guidelines
1. Controller Name must be in `CapitalCase` and must follow `Controller` suffix
2. Function inside the Controller also must be in `CapitalCase` and must follow `Action` suffix
3. Table in Database and it's respective Model Class name must be in pural.

## Getting Started

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

#### Many More Features
There are many more features of SimpleFramework, that is not covered here. please go through this framework and see for yourself :) 
