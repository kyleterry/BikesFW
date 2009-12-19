<?php

/**
 *
 * @author Kyle Terry <kyle@kyleterry.com>
 * @class bikes_Cache_Adapter_Apc
 */
class bikes_Cache_Adapter_Apc extends bikes_Cache_Abstract{
	
	/**
	 * 
	 * @var unknown_type
	 * @static
	 * @access private
	 */
	private static $instance;

	/**
	 *
	 * @var bikes_App $app
	 */
	protected $app;

	/**
	 *
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}
	
	/**
	 * 
	 * @static
	 * @access public
	 * @return unknown_type
	 */
	public static function getInstance(){
		if(!self::$instance instanceof self){
			self::$instance = new self(bikes_App::getInstance());
		}
		return self::$instance;
	}

	/**
	 * (non-PHPdoc)
	 * @see Cache/bikes_Cache_Abstract#connect()
	 */
	public function connect(){}

	/**
	 *
	 * @param mixed $index
	 * @param mixed $value
	 * @param integer $ttl (time to live)
	 * @return true on success|false on failure
	 */
	public function add($index, $value, $ttl = 10){
		if(!apc_fetch($index)){
			apc_store($index, $value, $ttl);
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param mixed $index
	 * @return mixed $value on success|false on failure
	 */
	public function get($index){
		if(!$value = apc_fetch($index)){
			return false;
		}
		return $value;
	}
	
	/**
	 * 
	 * @param string $index
	 * @return void
	 */
	public function delete($index){
		apc_delete($index);
	}

	/**
	 *
	 * @param mixed $index
	 * @return true on success
	 * @return false on failure
	 */
	public function exists($index){
		if(!apc_fetch($index)){
			return false;
		}
		return true;
	}

	public function flush(){

	}
}
