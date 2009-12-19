<?php

/**
 * Abstract VO class
 * 
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009 BikesFW Bikes Framework
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_VO_Abstract{

	public function __construct(){}

	public function __toString(){
		return $this->id;
	}
}
