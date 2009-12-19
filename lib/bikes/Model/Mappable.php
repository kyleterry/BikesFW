<?php

/**
 * bikes_Model_Mappable{ 
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
interface bikes_Model_Mappable{

	/**
	 * addModel 
	 * 
	 * @access public
	 * @return void
	 */
	public function addModel();

	/**
	 * fetchMasterMeta 
	 * 
	 * @access public
	 * @return void
	 */
	public function fetchMasterMeta();
}
