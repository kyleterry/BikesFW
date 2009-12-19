<?php

class bikes_Slug{
	
	/**
	 * 
	 * @var bikes_App
	 */
	protected $app;
	
	/**
	 * 
	 * @return void
	 */
	public function __construct(){
		$this->app = $app;
	}
	
	/**
	 * 
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
