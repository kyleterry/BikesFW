<?php

/**
 * bikes_Service_Abstract{ 
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Service_Abstract{
	
	/**
	 * Holds an instance of bikes_App 
	 * 
	 * @var bikes_App $app
	 * @access protected
	 */
	protected $app;

	/**
	 * __construct 
	 * 
	 * @param bikes_App $app 
	 * @access public
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}
}
