<?php

/**
 * Class to generate a URL Slug
 * TODO needs utf-8 and general unicode support
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Slug{
	
	/**
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
	}
	
	/**
	 * 
	 * @static
	 * @access protected
	 * @param string $value
	 * @return string
	 */
	public static function generate($value){
		$value = (string) $value;
		
		$value = str_replace(array('"', "'"), '', $value);
		$value = preg_replace('/[^a-zA-Z0-9]/D', ' ', $value);
		$value = preg_replace('/\s\s+/', ' ', $value);
		$value = str_replace(' ', '-', $value);
		$value = strtolower($value);
		$value = trim($value, '-');
		return $value;
	}
}
