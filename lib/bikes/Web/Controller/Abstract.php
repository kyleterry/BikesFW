<?php

abstract class bikes_Web_Controller_Abstract{

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
