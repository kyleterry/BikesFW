<?php

/**
 * This request object is no where near complete.
 * 
 * @package BikeFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Request{

	/**
	 * Holds an instance of bikes_App
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $app;

	/**
	 * Oh fuck yeah! A CTOR!
	 *
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	/**
	 * getFiltered 
	 * 
	 * @param string $source 
	 * @param array $filter_params 
	 * @access public
	 * @return fasle on failure
	 * @return mixed on success
	 */
	public function getFiltered($source, array $filter_params = null){
		$raw = $this->getRaw($source);
		//$ids = new bikes_Filter_Input($this->app, $this, $source);
		//if(true === $ids->confirmInput()){
			return $raw;
		//}
		return false;
	}

	/**
	 * Get unfiltered values from PHP global request variables.
	 *
	 * @return array|bool false on error
	 */
	public function getRaw($source){
		switch (strtoupper($source)) {
			case 'GLOBALS':
				return $GLOBALS;
				break;

			case 'GET':
				return $_GET;
				break;

			case 'POST':
				return $_POST;
				break;

			case 'COOKIE':
				return $_COOKIE;
				break;

			case 'FILES';
				return $_FILES;

			case 'SERVER':
				return $_SERVER;
				break;

			case 'ENV':
				return $_ENV;
				break;

			default:
				return false;
		}
	}

	/**
	 * Get json data from a stream and decode it into an array
	 *
	 * @param string $source
	 * @return array $json
	 */
	public function getJson($source = 'php://input'){
		$data = file_get_contents($source);
		if(false === $data){
			throw new Exception('That is not json!');
		}
		$json = json_decode(trim($data), true);
		if(empty($json) OR false === $json){
			throw new Exception('Invalid Json data');
		}
		return $json;
	}

	/**
	 * Returns the URL the application is currently working on.
	 * if $with_uri is set to true, it will return the URI
	 * plus any query strings attached.
	 *
	 * @param boolean $with_uri
	 * @return string
	 */
	public static function getURL($with_uri = true){
		if(!empty($_SERVER['HTTPS'])){
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		if(true === $with_uri){
			return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		} else {
			return $protocol.'://'.$_SERVER['HTTP_HOST'];
		}
	}

	/**
	 * getUri 
	 * 
	 * @access public
	 * @return string
	 */
	public function getUri(){
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * Wraps $this->getUri() for lazy people. I know how APIs can get.
	 * 
	 * @access public
	 * @return void
	 */
	public function getRequestUri(){
		return $this->getUri();
	}

	/**
	 * Check to see if POST data exists.
	 *
	 * @return true on success
	 * @return false on failue (aka not a POST request)
	 */
	public function isPostRequest(){
		if('POST' === $_SERVER['REQUEST_METHOD']){
			return true;
		}
		return false;
	}
}
