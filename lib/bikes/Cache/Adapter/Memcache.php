<?php

/**
 * bikes_Cache_Adapter_Memcache 
 * 
 * @uses bikes
 * @uses _Cache_Abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com 
 * @license BSD (Inlcuded)
 */
class bikes_Cache_Adapter_Memcache extends bikes_Cache_Abstract{

	/**
	 * app 
	 * 
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;

	protected $memcache;

	public function __construct(bikes_App $app, $host = null, $port = null){
		$this->app = $app;
		$this->connect($host, $port);
	}

	public function connect($host = null, $port = null){
		$this->memcache = new Memcache;
		if(!empty($host)){
			if(!empty($port)){
				$this->memcache->connect($host, $port);
			} else {
				$this->memcache->connect($host);
			}
		} else {
			$memcacheHosts = $this->app->getConfig('memcache.host');
			if(empty($memcacheHosts)){
				throw new Exception('If there are no memecache servers set in your config.ini, you must pass one into the bikes_Cache_Adapter_Memcache::__construct');
			}
			foreach($this->app->getConfig('memcache.host') as $hostPort){
				$server = explode(':', $hostPort);
				$this->memcache->addServer($server[0], $server[1]);
			}
		}
	}

	public function add($index, $value, $ttl = 1500){
		$this->memcache->add($index, $value, false, $ttl);
	}

	public function append($index, $value){
		if(!$this->memcache->get($index)){
			$this->add($index, $value);
		} else {
			$this->memcache->append($index, $value);
		}
	}

	public function get($index){
		return $this->memcache->get($index);
	}

	public function flush(){
		$this->memcache->flush();
	}

	public function stats(){
		return $this->memcache->getExtendedStats('slabs');
	}
}
