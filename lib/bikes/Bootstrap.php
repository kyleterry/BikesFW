<?php

/**
 * 
 * Includes the autoloader 
 */
require_once LIB_PATH . DS . 'bikes' . DS . 'Autoload.php';

/**
 * bikes_Bootstrap
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded) http://trythisbike.org/LICENSE
 */
class bikes_Bootstrap{
	
	/**
	 * This will register the autoloader and the exception handler.
	 * the app process is started here as well.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		spl_autoload_register('bikes_Autoload::load');
		//set_exception_handler(array('bikes_ExceptionHandler','handle'));
		$this->init();
	}

	/**
	 * This will create an app object and let her rip.
	 * 
	 * @access public
	 * @return void
	 */
	public function init(){
		if(!$app = apc_fetch('app')){
			$config = new bikes_Config;
			$app = new bikes_App($config);
			apc_store('app', $app);
		}
		$app->init();
		echo $app->dispatch();
	}
}
