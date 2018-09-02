<?php

class AutoLoader {

	/**
	 * Used in the loadGlobal method
	 *
	 * @var array
	 */
	public static $directories = array('Controllers', 'Models');

	/**
	 * load class file by it's full name
	 *
	 * @param string $class
	 * @return void
	 */
	public static function Load($uri)
	{ 

	   $class = str_replace('\\' , DS , $uri);


        if(file_exists($class.'.php')) //BASE_PATH.DS.
        {
            require_once($class.'.php');
        }
        elseif(file_exists(LANG_PATH.DS.$class.'.php'))
        {
            require_once(LANG_PATH.DS.$class.'.php');
        }
        elseif(file_exists(LANG_PATH.DS."English".$class.'.php'))
        {
            require_once(LANG_PATH.DS.$class.'.php');
        }
        elseif(file_exists(LANG_PATH.DS."Russian".$class.'.php'))
        {
            require_once(LANG_PATH.DS.$class.'.php');
        }
	    elseif(file_exists(APP_PATH.DS.$class.'.php'))
        {
           require_once(APP_PATH.DS.$class.'.php');
        }
        elseif(file_exists(BASE_PATH.DS.$class.'.php'))
        {
            require_once(BASE_PATH.DS.$class.'.php');
        }

        elseif(file_exists(BASE_PATH.DS.APP_PATH.DS.$class.'.php'))//BASE_PATH.DS.
        {
            require_once($class.'.php');
        }

        else
        {
            var_dump("$class.php");
            //throw new \Exception("Unable to load the requested class file: $class.php");
        }
	}

	/**
	 * Must be called before using any class in the Application folder
	 *
	 * @return void
	 */
	public static function loadGlobal()
	{
		if(!empty(self::$directories))
		{
			foreach (self::$directories as $directory) {
				$handle = opendir(path('app').'\\' .PANEL.'\\' . $directory);
				while (false !== ($entry = readdir($handle))) {
					if($entry != "." && $entry != "..")
					{
                        require_once path('app').'\\' .PANEL.'\\' .$directory.'\\'.$entry;
					}
				}
			}
			self::$directories = array();
		}
	}
}