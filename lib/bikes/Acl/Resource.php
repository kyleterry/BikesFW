<?php

/**
 * bikes_Acl_Resource{ 
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Acl_Resource{
	
	/**
	 * name 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $name;

	/**
	 * __construct 
	 * 
	 * @param string $name 
	 * @access public
	 * @return void
	 */
	public function __construct($name){
		$this->name = $name;
	}

	/**
	 * getName 
	 * 
	 * @access public
	 * @return void
	 */
	public function getName(){
		return $this->name;
	}
}
