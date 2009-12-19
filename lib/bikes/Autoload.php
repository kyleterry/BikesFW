<?php

/**
 * bikes_Autoload
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Autoload{
	
	/**
	 * Lonely Ctor
	 *
	 * @return void
	 */
	public function __construct(){}

	/**
	 * Publically accessible init method to test what kind of class
	 * the autoloader is loading.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public static function load($name){
		if(0 === strncmp($name, 'bikes', 5) &&
		   0 !== strncmp(substr($name, -9), 'Exception', 9)){
			return self::loadCore($name);
		} elseif(0 === strncmp(substr($name, -9), 'Exception', 9)){
			return self::loadException($name);
		} else {
			$len = strlen(PROJECT);
			if(0 === strncmp($name, PROJECT, $len)){
				return self::loadApp($name);
			}
			return self::loadVendor($name);
		}
	}

	/**
	 * Loads core BikesFW files
	 *
	 * @param string $name
	 * @return true on success
	 */
	protected static function loadCore($name){
		$path = LIB_PATH . DS . str_replace('_', DS, $name) . '.php';
		if(!file_exists($path)){
			throw new bikes_AutoloaderCouldNotFindCoreFileException('The class: ' . $name . ' does not have a matching file for the autoloader to include.');
		}
		include_once $path;
		return true;
	}

	/**
	 * Loads core Exception files
	 *
	 * @param string $name
	 * @return true on success
	 */
	protected static function loadException($name){
		$class = str_replace('_', DS, $name);
		$class = trim(strstr($class, DS), DS);
		$path = LIB_PATH . DS . 'bikes' . DS . 'Exception' . DS . $class . '.php';
		if(!file_exists($path)){
			throw new bikes_AutoloaderCouldNotFindExceptionFileException('The Exception: ' . $name . ' does not have a matching file for the autoloader to include.');
		}
		include_once $path;
		return true;
	}

	/**
	 * Loads project/application files
	 *
	 * @param string $name
	 * @return true on success
	 */
	protected static function loadApp($name){
		$origName = $name;
		$name = explode('_', $name);
		unset($name[0]);
		$name = implode('_', $name);
		$path = PROJECT_PATH . DS . str_replace('_', DS, $name) . '.php';
		if(!file_exists($path)){
			throw new bikes_AutoloadException('The application loader could not find the ' . $path);
		}
		include_once $path;
		return true;
	}

	/**
	 * loadVendor 
	 * 
	 * @param mixed $name 
	 * @static
	 * @access protected
	 * @return boolean true on success
	 */
	protected static function loadVendor($name){
		$path = LIB_PATH . DS . 'bikes' . DS . 'vendors' . DS . str_replace('_', DS, $name) . '.php';
		if(!file_exists($path)){
			throw new bikes_AutoloaderCountNotFileVendorClassException('yeah');
		}
		include_once $path;
		return true;
	}
}
