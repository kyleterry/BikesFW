<?php

/**
 * bikes_Session_Adapter_Db 
 * 
 * @uses bikes_Session_Abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Session_Adapter_Db extends bikes_Session_Abstract{

	/**
	 * started 
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $started = false;

	/**
	 * destroyed 
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $destroyed = false;

	/**
	 * table 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $table;

	/**
	 * cookieName 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cookieName;

	/**
	 * sessionKey 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $sessionKey;

	/**
	 * fingerprint 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $fingerprint;

	/**
	 * result 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $result;

	/**
	 * sessionData 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $sessionData = array();
	
	/**
	 * start 
	 * 
	 * @access public
	 * @return void
	 */
	public function start(){
		if(empty($this->table)){
			throw new bikes_SessionException('You must set a table before trying to call ::start() in the Db session adapter');
		}

		if(empty($this->cookieName)){
			throw new bikes_SessionException('You must set a cookie name before trying to call ::start() in the Db session adapter');
		}

		if(empty($this->fingerprint)){
			$this->fingerprint();
		}

		if(false === $this->destroyed && $this->getKeyFromCookie()){
			if($this->fetch()){
				$this->destroyed = false;
				$this->started = true;
				return true;
			} else {
				$this->destroy();
				return $this->start();
			}
		}

		$this->generateSessionKey();
		$this->setCookie();
		if($this->create()){
			$this->destroyed = false;
			$this->started = true;
			return true;
		}
	}

	/**
	 * isStarted 
	 * 
	 * @access public
	 * @return true|false
	 */
	public function isStarted(){
		return $this->started;
	}

	/**
	 * setTable 
	 * 
	 * @param string $table 
	 * @access public
	 * @return self
	 */
	public function setTable($table){
		if(is_string($table) && empty($this->table)){
			$this->table = $table;
		}
		return $this;
	}

	/**
	 * setCookieName 
	 * 
	 * @param mixed $name 
	 * @access public
	 * @return self
	 */
	public function setCookieName($name){
		if(!is_string($name)){
			throw new bikes_SessionException('Cookie name must be a string.');
		}
		$this->cookieName = $name;
		return $this;
	}

	/**
	 * Checks the existance of a session cookie and sets it in a class property.
	 * 
	 * @access public
	 * @return false if it doesn't exist
	 * @return true
	 */
	protected function getKeyFromCookie(){
		$request = $this->app->getRequest();
		$cookie = $request->getRaw('COOKIE');
		if(!isset($cookie[$this->cookieName])){
			return false;
		}
		$this->sessionKey = $cookie[$this->cookieName];
		return true;
	}

	/**
	 * fingerprint 
	 * 
	 * @access public
	 * @return true
	 */
	protected function fingerprint(){
		$fingerprint = $_SERVER['HTTP_ACCEPT_ENCODING'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_USER_AGENT'];
		$this->fingerprint = hash('SHA256', $fingerprint);
		return true;
	}

	/**
	 * generateSessionKey 
	 * 
	 * @access public
	 * @return self
	 */
	protected function generateSessionKey(){
		if(empty($this->sessionKey)){
			$key = sha1(mt_rand()) . microtime();
			$this->sessionKey = hash('SHA256', $key);
			return $this;
		}
		return $this;
	}

	/**
	 * Sets the session cookie
	 * 
	 * @access protected
	 * @return void
	 */
	protected function setCookie(){
		$response = $this->app->getResponse();
		
		$cookie = array(
			'name'  => $this->cookieName,
			'value'	=> $this->sessionKey,
			'path'  => '/',
			'domain'=> $this->app->getDomain()
		);

		$response->setCookie($cookie);
	}

	/**
	 * create 
	 * 
	 * @access protected
	 * @return true|false
	 */
	protected function create(){
		$sql = 'INSERT INTO `%s` (`sessionKey`, `fingerprint`, `sessionData`) VALUES (:sessionKey, :fingerprint, :sessionData)';
		$sql = sprintf($sql, $this->table);

		$db = $this->app->getDb();
		$statement = $db->prepare($sql);
		$statement->bindValue(':sessionKey', $this->sessionKey);
		$statement->bindValue(':fingerprint', $this->fingerprint);
		$sessionData = serialize($this->sessionData);
		$statement->bindValue(':sessionData', $sessionData);

		if($statement->execute()){
			return true;
		}
		return false;
	}

	/**
	 * Fetched the session from cache or the database 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function fetch(){
		$sql = 'SELECT * FROM `%s` WHERE `sessionKey` = :sessionKey AND `fingerprint` = :fingerprint AND `deleted` = 0';
		$sql = sprintf($sql, $this->table);
		$db = $this->app->getDb();

		$statement = $db->prepare($sql);
		$statement->bindValue(':sessionKey', $this->sessionKey);
		$statement->bindValue(':fingerprint', $this->fingerprint);
		$statement->execute();
		$this->result = $statement->fetchAll(PDO::FETCH_ASSOC);

		if(1 < count($this->result) || 0 === count($this->result)){
			return false;
		}

		if(!empty($this->result[0]['sessionData'])){
			$this->sessionData = unserialize($this->result[0]['sessionData']);
		}
		return true;
	}

	/**
	 * save 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function save(){
		$sql = 'UPDATE `%s` SET `sessionData` = :sessionData WHERE `sessionKey` = :sessionKey AND `fingerprint` = :fingerprint AND `deleted` = 0;';
		$sql = sprintf($sql, $this->table);
		$db = $this->app->getDb();
		$sessionData = serialize($this->sessionData);
		$statement = $db->prepare($sql);
		$statement->bindValue(':sessionData', $sessionData);
		$statement->bindValue(':sessionKey', $this->sessionKey);
		$statement->bindValue(':fingerprint', $this->fingerprint);
		if($statement->execute()){
			return true;
		}
		return false;
	}

	/**
	 * destroy 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function destroy(){
		$request = $this->app->getRequest();
		$cookie = $request->getRaw('COOKIE');
		if(isset($cookie[$this->cookieName])){
			unset($cookie[$this->cookieName]);
		}
		$this->destroyed = true;
		unset($this->sessionKey);
		unset($this->sessionData);
		$this->sessionData = array();
	}

	/**
	 * read 
	 * 
	 * @param string $index 
	 * @access protected
	 * @return false if no index exists
	 * @return mixed if data exists.
	 */
	public function read($index){
		if(isset($this->sessionData[$index])){
			return $this->sessionData[$index];
		}
		return false;
	}

	/**
	 * write 
	 * 
	 * @param mixed $index 
	 * @param mixed $value 
	 * @access protected
	 * @return true|false
	 */
	public function write($index, $value){
		$this->sessionData[$index] = $value;
		if(isset($this->sessionData[$index])){
			return true;
		}
		return false;
	}
}
